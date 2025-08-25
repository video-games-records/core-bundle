<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\MessageHandler\Player;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerChartRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Event\PlayerChartUpdated;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerChartRank;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerGroupRank;
use VideoGamesRecords\CoreBundle\Message\Team\UpdateTeamChartRank;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use Zenstruck\Messenger\Monitor\Stamp\DescriptionStamp;

#[AsMessageHandler]
readonly class UpdatePlayerChartRankHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher,
        private PlayerChartRankingProvider $playerChartRankingProvider,
        private MessageBusInterface $bus
    ) {
    }

    /**
     * @throws ORMException
     * @throws ExceptionInterface
     */
    public function __invoke(UpdatePlayerChartRank $updatePlayerChartRank): array
    {
        /** @var Chart $chart */
        $chart = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')
            ->find($updatePlayerChartRank->getChartId());
        if (null == $chart) {
            return ['error' => 'chart not found'];
        }

        $ranking = $this->playerChartRankingProvider->getRanking(
            $chart,
            ['orderBy' => PlayerChartRankingProvider::ORDER_BY_SCORE]
        );
        $pointsChart = Ranking::chartPointProvider(count($ranking));

        $topScoreLibValue = '';
        $previousLibValue = '';
        $rank = 1;
        $nbEqual = 1;
        $playerChartEqual = [];
        $platforms = [];
        $result = $this->getPlatforms($chart);

        foreach ($result as $row) {
            $platforms[$row['id']] = [
                'count' => $row['nb'],
                'points' => Ranking::platformPointProvider($row['nb']),
                'previousLibValue' => '',
                'rank' => 0,
                'nbEqual' => 1,
                'playerChartEqual' => [],
            ];
        }

        foreach ($ranking as $k => $item) {
            $libValue = '';
            /** @var PlayerChart $playerChart */
            $playerChart = $item[0];

            // Lost position ?
            $oldRank = $playerChart->getRank();
            $oldNbEqual = $playerChart->getNbEqual();
            $playerChart->setIsTopScore(false);

            foreach ($chart->getLibs() as $lib) {
                $libValue .= $item['value_' . $lib->getId()] . '/';
            }
            if ($k === 0) {
                // Premier élément => topScore
                $playerChart->setIsTopScore(true);
                $topScoreLibValue = $libValue;
            } else {
                if ($libValue === $topScoreLibValue) {
                    $playerChart->setIsTopScore(true);
                }
                if ($previousLibValue === $libValue) {
                    ++$nbEqual;
                } else {
                    $rank += $nbEqual;
                    $nbEqual = 1;
                    $playerChartEqual = [];
                }
            }
            // Platform point
            if ($playerChart->getPlatform() != null) {
                $idPlatForm = $playerChart->getPlatform()->getId();
                if ($platforms[$idPlatForm]['previousLibValue'] === $libValue) {
                    ++$platforms[$idPlatForm]['nbEqual'];
                } else {
                    $platforms[$idPlatForm]['rank'] += $platforms[$idPlatForm]['nbEqual'];
                    $platforms[$idPlatForm]['nbEqual'] = 1;
                    $platforms[$idPlatForm]['playerChartEqual'] = [];
                }
                $platforms[$idPlatForm]['playerChartEqual'][] = $playerChart;
            }

            $playerChartEqual[] = $playerChart;

            $playerChart->setNbEqual($nbEqual);
            $playerChart->setRank($rank);
            $playerChart->setPointChart((int) (
                array_sum(
                    array_slice(array_values($pointsChart), $playerChart->getRank() - 1, $playerChart->getNbEqual())
                ) / $playerChart->getNbEqual()
            ));

            if ($nbEqual > 1) {
                // Pour les égalités déjà passées on met à jour le nbEqual et l'attribution des points
                foreach ($playerChartEqual as $playerChartToModify) {
                    $playerChartToModify->setNbEqual($nbEqual);
                    $playerChartToModify->setPointChart($playerChart->getPointChart());
                }
            }

            if ($playerChart->getPlatform() != null) {
                $idPlatForm = $playerChart->getPlatform()->getId();
                $playerChart->setPointPlatform((int) (
                    array_sum(
                        array_slice(
                            array_values($platforms[$idPlatForm]['points']),
                            $platforms[$idPlatForm]['rank'] - 1,
                            $platforms[$idPlatForm]['nbEqual']
                        )
                    ) / $platforms[$idPlatForm]['nbEqual']
                ));
                if ($platforms[$idPlatForm]['nbEqual'] > 1) {
                    // Pour les égalités déjà passées on met à jour le nbEqual et l'attribution des points
                    foreach ($platforms[$idPlatForm]['playerChartEqual'] as $playerChartToModify) {
                        $playerChartToModify
                            ->setPointPlatform($playerChart->getPointPlatform());
                    }
                }
            } else {
                $playerChart->setPointPlatform(0);
            }

            $this->eventDispatcher->dispatch(new PlayerChartUpdated($playerChart, $oldRank, $oldNbEqual));

            $previousLibValue = $libValue;

            // Platform point
            if ($playerChart->getPlatform() != null) {
                $platforms[$playerChart->getPlatform()->getId()]['previousLibValue'] = $libValue;
            }
        }
        $this->em->flush();

        $this->bus->dispatch(
            new UpdatePlayerGroupRank($chart->getGroup()->getId()),
            [
                new DescriptionStamp(
                    sprintf('Update player-ranking for group [%d]', $chart->getGroup()->getId())
                )
            ]
        );
        $this->bus->dispatch(
            new UpdateTeamChartRank($chart->getId()),
            [
                new DescriptionStamp(
                    sprintf('Update team-ranking for chart [%d]', $chart->getId())
                )
            ]
        );

        return ['success' => true];
    }


    /**
     * @param Chart $chart
     * @return int|mixed|string
     */
    private function getPlatforms(Chart $chart): mixed
    {
        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 COUNT(pc) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            INNER JOIN pc.platform p
            WHERE pc.chart = :chart
            GROUP BY p.id");

        $query->setParameter('chart', $chart);
        return $query->getResult(2);
    }
}
