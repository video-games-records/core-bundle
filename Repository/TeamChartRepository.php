<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\TeamChart;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

/**
 * TeamChartRepository
 */
class TeamChartRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamChart::class);
    }

    /**
     * @param Chart $chart
     * @param null  $maxRank
     * @param null  $team
     * @return array
     */
    public function getRankingPoints(Chart $chart, $maxRank = null, $team = null)
    {
        $query = $this->createQueryBuilder('tc')
            ->join('tc.team', 't')
            ->addSelect('t')
            ->orderBy('tc.rankPointChart');

        $query->where('tc.chart = :chart')
            ->setParameter('chart', $chart);

        if (($maxRank !== null) && ($team !== null)) {
            $query->andWhere('(tc.rankPointChart <= :maxRank OR tc.team = :team)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('team', $team);
        } elseif ($maxRank !== null) {
            $query->andWhere('tc.rankPointChart <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }
        return $query->getQuery()->getResult();
    }


    /**
     * @param $chart
     * @return array
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ExceptionInterface
     */
    public function maj($chart)
    {
        /** @var Chart $chart */

        $teams = [];

        //----- delete
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\TeamChart tc WHERE tc.chart = :chart');
        $query->setParameter('chart', $chart);
        $query->execute();

        $query = $this->_em->createQuery("
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
            $teams[$playerChart->getPlayer()->getTeam()->getId()] = $playerChart->getPlayer()->getTeam();
            $idTeam = $playerChart->getPlayer()->getTeam()->getId();
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
            $teamChart->setTeam($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['idTeam']));
            $teamChart->setChart($chart);

            $this->_em->persist($teamChart);
        }

        $chart->setStatusTeam(Chart::STATUS_NORMAL);
        $this->_em->flush();

        return $teams;
    }
}
