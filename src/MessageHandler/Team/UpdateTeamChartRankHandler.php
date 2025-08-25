<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\MessageHandler\Team;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Message\Team\UpdateTeamChartRank;
use VideoGamesRecords\CoreBundle\Message\Team\UpdateTeamGroupRank;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use Zenstruck\Messenger\Monitor\Stamp\DescriptionStamp;

#[AsMessageHandler]
readonly class UpdateTeamChartRankHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
    ) {
    }

    /**
     * @throws ORMException
     * @throws ExceptionInterface
     */
    public function __invoke(UpdateTeamChartRank $updateTeamChartRank): array
    {
        $chart = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')
            ->find($updateTeamChartRank->getChartId());
        if (null == $chart) {
            return ['error' => 'chart not found'];
        }

        //----- delete
        $query = $this->em
            ->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\TeamChart tc WHERE tc.chart = :chart');
        $query->setParameter('chart', $chart);
        $query->execute();

        $query = $this->em->createQuery("
            SELECT pc
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN p.team t
            WHERE pc.chart = :chart
            ORDER BY pc.pointChart DESC");

        $query->setParameter('chart', $chart);
        $result = $query->getResult();

        $list = array();
        foreach ($result as $playerChart) {
            $team = $playerChart->getPlayer()->getTeam();

            $idTeam = $team->getId();
            if (!isset($list[$idTeam])) {
                $list[$idTeam] = [
                    'idTeam' => $playerChart->getPlayer()->getTeam()->getId(),
                    'nbPlayer' => 1,
                    'pointChart' => $playerChart->getPointChart(),
                    'chartRank0' => 0,
                    'chartRank1' => 0,
                    'chartRank2' => 0,
                    'chartRank3' => 0,
                ];
            } elseif ($list[$idTeam]['nbPlayer'] < 5) {
                $list[$idTeam]['nbPlayer']   += 1;
                $list[$idTeam]['pointChart'] += $playerChart->getPointChart();
            }
        }

        //----- add some data
        $list = array_values($list);
        $list = Ranking::order($list, ['pointChart' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        $nbTeam = count($list);

        foreach ($list as $row) {
            //----- add medals
            if ($row['rankPointChart'] == 1 && $row['nbEqual'] == 1 && $nbTeam > 1) {
                $row['chartRank0'] = 1;
                $row['chartRank1'] = 1;
            } elseif ($row['rankPointChart'] == 1 && $row['nbEqual'] == 1 && $nbTeam == 1) {
                $row['chartRank1'] = 1;
            } elseif ($row['rankPointChart'] == 1 && $row['nbEqual'] > 1) {
                $row['chartRank1'] = 1;
            } elseif ($row['rankPointChart'] == 2) {
                $row['chartRank2'] = 1;
            } elseif ($row['rankPointChart'] == 3) {
                $row['chartRank3'] = 1;
            }

            $teamChart = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\TeamChart'
            );
            $teamChart->setTeam(
                $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['idTeam'])
            );
            $teamChart->setChart($chart);

            $this->em->persist($teamChart);
        }

        $this->em->flush();

        $this->bus->dispatch(
            new UpdateTeamGroupRank($chart->getGroup()->getId()),
            [
                new DescriptionStamp(
                    sprintf('Update team-ranking for group [%d]', $chart->getGroup()->getId())
                )
            ]
        );
        return ['success' => true];
    }
}
