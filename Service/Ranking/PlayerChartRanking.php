<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\LostPosition;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Interface\RankingInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerChartRanking
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function maj($id): void
    {
        $chart = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->find($id);
        if (null === $chart) {
            return;
        }

        /** @var Chart $chart */
        $chart       = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')->getWithChartType($chart);
        $ranking     = $this->getRankingForUpdate($chart);
        $pointsChart = Ranking::chartPointProvider(count($ranking));
        //$players     = [];

        $topScoreLibValue = '';
        $previousLibValue = '';
        $rank             = 1;
        $nbEqual          = 1;
        $playerChartEqual = [];

        $result = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerChart')->getPlatforms($chart);
        $platforms = [];
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

            $players[$playerChart->getPlayer()->getId()]  = $playerChart->getPlayer();
            $playerChart->setTopScore(false);

            foreach ($chart->getLibs() as $lib) {
                $libValue .= $item['value_' . $lib->getIdLibChart()] . '/';
            }
            if ($k === 0) {
                // Premier élément => topScore
                $playerChart->setTopScore(true);
                $topScoreLibValue = $libValue;
            } else {
                if ($libValue === $topScoreLibValue) {
                    $playerChart->setTopScore(true);
                }
                if ($previousLibValue === $libValue) {
                    ++$nbEqual;
                } else {
                    $rank             += $nbEqual;
                    $nbEqual          = 1;
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

            $playerChart
                ->setNbEqual($nbEqual)
                ->setRank($rank)
                ->setPointChart((int)(
                    array_sum(
                        array_slice(array_values($pointsChart), $playerChart->getRank() - 1, $playerChart->getNbEqual())
                    ) / $playerChart->getNbEqual()
                ));

            if ($nbEqual > 1) {
                // Pour les égalités déjà passées on met à jour le nbEqual et l'attribution des points
                foreach ($playerChartEqual as $playerChartToModify) {
                    $playerChartToModify
                        ->setNbEqual($nbEqual)
                        ->setPointChart($playerChart->getPointChart());
                }
            }

            if ($playerChart->getPlatform() != null) {
                $idPlatForm = $playerChart->getPlatform()->getId();
                $playerChart->setPointPlatform((int)(
                    array_sum(
                        array_slice(array_values($platforms[$idPlatForm]['points']), $platforms[$idPlatForm]['rank'] - 1, $platforms[$idPlatForm]['nbEqual'])
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

            // Lost position ?
            $newRank = $playerChart->getRank();
            $newNbEqual = $playerChart->getNbEqual();

            if ((($oldRank >= 1) && ($oldRank <= 3) && ($newRank > $oldRank)) ||
                (($oldRank === 1) && ($oldNbEqual === 1) && ($newRank === 1) && ($newNbEqual > 1))
            ) {
                $lostPosition = new LostPosition();
                $lostPosition->setNewRank($newRank);
                $lostPosition->setOldRank(($oldNbEqual == 1 && $oldRank == 1) ? 0 : $oldRank); //----- zero for losing platinum medal
                $lostPosition->setPlayer($this->em->getReference(Player::class, $playerChart->getPlayer()->getId()));
                $lostPosition->setChart($this->em->getReference(Chart::class, $playerChart->getChart()->getId()));
                $this->em->persist($lostPosition);
            }

            $previousLibValue = $libValue;

            // Platform point
            if ($playerChart->getPlatform() != null) {
                $platforms[$playerChart->getPlatform()->getId()]['previousLibValue'] = $libValue;
            }
        }
        $this->getEntityManager()->flush();
        //return $players;
    }

    public function get($id): void
    {
        // TODO: Implement get() method.
    }
}
