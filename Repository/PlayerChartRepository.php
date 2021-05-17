<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use DateInterval;
use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Entity\LostPosition;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\NonUniqueResultException;
use DateTime;

class PlayerChartRepository extends EntityRepository
{
    /**
     * @param int $idPlayer
     * @param int $idChart
     * @return PlayerChart
     * @throws NonUniqueResultException
     */
    public function getFromUnique(int $idPlayer, int $idChart)
    {
        $query = $this->createQueryBuilder('pc')
            ->join('pc.player', 'p')
            ->join('pc.chart', 'c')
            ->where('p.id = :idPlayer')
            ->setParameter('idPlayer', $idPlayer)
            ->andWhere('c.id = :idChart')
            ->setParameter('idChart', $idChart);

        return $query->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @param Chart $chart
     * @param null  $maxRank
     * @param null  $player
     * @param null  $team
     * @return array
     */
    public function getRankingPoints(Chart $chart, $maxRank = null, $player = null, $team = null)
    {
        $query = $this->createQueryBuilder('pc')
            ->join('pc.player', 'p')
            ->addSelect('p')
            ->orderBy('pc.rank');

        $query->where('pc.chart = :chart')
            ->setParameter('chart', $chart);

        if ($team != null) {
            $query->andWhere('(p.team = :team)')
                ->setParameter('team', $team);
        } elseif (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pg.rankPointChart <= :maxRank OR pc.player= :player)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } elseif ($maxRank !== null) {
            $query->andWhere('pc.rank <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param Chart $chart
     * @return array
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function maj(Chart $chart)
    {
        /** @var Chart $chart */
        $chart       = $this->_em->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($chart);
        $ranking     = $this->getRankingForUpdate($chart);
        $pointsChart = Ranking::chartPointProvider(count($ranking));
        $players     = [];

        $topScoreLibValue = '';
        $previousLibValue = '';
        $rank             = 1;
        $nbEqual          = 1;
        $playerChartEqual = [];

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

            // Lost position ?
            $newRank = $playerChart->getRank();
            $newNbEqual = $playerChart->getNbEqual();

            if ((($oldRank >= 1) && ($oldRank <= 3) && ($newRank > $oldRank)) ||
                (($oldRank === 1) && ($oldNbEqual === 1) && ($newRank === 1) && ($newNbEqual > 1))
            ) {
                $lostPosition = new LostPosition();
                $lostPosition->setNewRank($newRank);
                $lostPosition->setOldRank(($oldNbEqual == 1 && $oldRank == 1) ? 0 : $oldRank); //----- zero for losing platinum medal
                $lostPosition->setPlayer($this->_em->getReference(Player::class, $playerChart->getPlayer()->getId()));
                $lostPosition->setChart($this->_em->getReference(Chart::class, $playerChart->getChart()->getId()));
                $this->_em->persist($lostPosition);
            }

            $previousLibValue = $libValue;
        }

        $chart->setStatusPlayer(Chart::STATUS_NORMAL);
        $this->getEntityManager()->flush();

        return $players;
    }

    /**
     * Provides every playerChart for update purpose.
     *
     * @param Chart $chart
     *
     * @return array
     */
    public function getRankingForUpdate(Chart $chart)
    {
        $queryBuilder = $this->getRankingBaseQuery2($chart);
        $queryBuilder
            ->andWhere('status.boolRanking = 1');

        return $queryBuilder->getQuery()->getResult();
    }

     /**
     * Provides disabled list.
     *
     * @param Chart $chart
     *
     * @return array
     */
    public function getDisableRanking(Chart $chart)
    {
        $queryBuilder = $this->getRankingBaseQuery2($chart);
        $queryBuilder
            ->andWhere('status.boolRanking = 0');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Chart $chart
     * @param null  $player
     * @param null  $limit
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getRanking(Chart $chart, $player = null, $limit = null)
    {
        $queryBuilder = $this->getRankingBaseQuery($chart);
        $queryBuilder
            ->andWhere('status.boolRanking = 1');

        if (null !== $limit && null !== $player) {
            $playerChart = $this->getFromUnique($player->getId(), $chart->getId());
            if ($playerChart) {
                $rank = $playerChart->getRank();
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->orX(
                            '(pc.rank <= :maxRank)',
                            '(pc.rank IS NULL)',
                            '(:min <= pc.rank) AND (pc.rank <= :max)',
                            '(pc.player = :player)'
                        )
                    )
                    ->setParameter('maxRank', $limit)
                    ->setParameter('player', $player)
                    ->setParameter(':min', $rank - 5)
                    ->setParameter(':max', $rank + 5);
            } else {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->orX('(pc.rank <= :maxRank)', '(pc.rank IS NULL)', 'pc.player = :player')
                    )
                    ->setParameter('maxRank', $limit)
                    ->setParameter('player', $player);
            }
        } elseif (null !== $limit) {
            $queryBuilder
                ->andWhere('pc.rank <= :maxRank')
                ->setParameter('maxRank', $limit);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Chart $chart
     *
     * @return QueryBuilder
     */
    private function getRankingBaseQuery(Chart $chart)
    {
        $queryBuilder = $this->createQueryBuilder('pc');
        $queryBuilder
            ->innerJoin('pc.player', 'p')
            ->addSelect('p')
            ->innerJoin('pc.chart', 'c')
            ->innerJoin('pc.status', 'status')
            ->addSelect('status')
            ->where('c.id = :idChart')
            ->setParameter('idChart', $chart->getId())
            ->orderBy('pc.rank','ASC')
            ->addOrderBy('status.sOrder', 'ASC')
            ->addOrderBy('pc.lastUpdate', 'ASC');

        foreach ($chart->getLibs() as $lib) {
            $key             = 'value_' . $lib->getIdLibChart();
            $alias           = 'pcl_' . $lib->getIdLibChart();
            $subQueryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select(sprintf('%s.value', $alias))
                ->from('VideoGamesRecordsCoreBundle:PlayerChartLib', $alias)
                ->where(sprintf('%s.libChart = :%s', $alias, $key))
                ->andWhere(sprintf('%s.playerChart = pc', $alias))
                ->setParameter($key, $lib);

            $queryBuilder
                ->addSelect(sprintf('(%s) as %s', $subQueryBuilder->getQuery()->getDQL(), $key))
                ->setParameter($key, $lib);
        }

        return $queryBuilder;
    }

    /**
     * @param Chart $chart
     * @return QueryBuilder
     */
    private function getRankingBaseQuery2(Chart $chart)
    {
        $queryBuilder = $this->createQueryBuilder('pc');
        $queryBuilder
            ->innerJoin('pc.player', 'p')
            ->addSelect('p')
            ->innerJoin('pc.status', 'status')
            ->addSelect('status')
            ->where('pc.chart = :chart')
            ->setParameter('chart', $chart);

        foreach ($chart->getLibs() as $lib) {
            $key             = 'value_' . $lib->getIdLibChart();
            $alias           = 'pcl_' . $lib->getIdLibChart();
            $subQueryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select(sprintf('%s.value', $alias))
                ->from('VideoGamesRecordsCoreBundle:PlayerChartLib', $alias)
                ->where(sprintf('%s.libChart = :%s', $alias, $key))
                ->andWhere(sprintf('%s.playerChart = pc', $alias))
                ->setParameter($key, $lib);

            $queryBuilder
                ->addSelect(sprintf('(%s) as %s', $subQueryBuilder->getQuery()->getDQL(), $key))
                ->addOrderBy($key, $lib->getType()->getOrderBy())
                ->setParameter($key, $lib);
        }
        $queryBuilder
            ->addOrderBy('status.sOrder', 'ASC')
            ->addOrderBy('pc.lastUpdate', 'ASC');
        return $queryBuilder;
    }

    /**
     * @return int|mixed|string
     */
    public function getPlayerChartToDesactivate()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P14D'));

        $query = $this->createQueryBuilder('pc')
            ->where('pc.status = :idStatus')
            ->setParameter('idStatus', PlayerChartStatus::ID_STATUS_INVESTIGATION)
            ->andWhere('pc.dateInvestigation < :date')
            ->setParameter('date', $date->format('Y-m-d'));
        return $query->getQuery()->getResult();
    }


    /**
     * @return array
     */
    public function getDataRank()
    {
        $query = $this->_em->createQuery("
                    SELECT
                         p.id,
                         CASE WHEN pc.rank > 29 THEN 30 ELSE pc.rank END AS rank,
                         COUNT(pc.id) as nb
                    FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
                    JOIN pc.player p
                    WHERE pc.rank > 3            
                    GROUP BY p.id, rank");

        $result = $query->getResult();
        $data = array();
        foreach ($result as $row) {
            $data[$row['id']][$row['rank']] = $row['nb'];
        }
        return $data;
    }

    /**
     * @param DateTime $date1
     * @param DateTime $date2
     * @return array
     */
    public function getNbPostDay(DateTime $date1, DateTime $date2)
    {
        $query = $this->_em->createQuery("
            SELECT
                 p.id,
                 COUNT(pc.chart) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            WHERE pc.lastUpdate BETWEEN :date1 AND :date2
            GROUP BY p.id");


        $query->setParameter('date1', $date1);
        $query->setParameter('date2', $date2);
        $result = $query->getResult();

        $data = array();
        foreach ($result as $row) {
            $data[$row['id']] = $row['nb'];
        }
        return $data;
    }


    /**
     * @param $player
     * @param $game
     * @param $platform
     */
    public function majPlatform($player, $game, $platform)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->update('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->set('pc.platform', ':platform')
            ->where('pc.player = :player')
            ->setParameter('platform', $platform)
            ->setParameter('player', $player)
            ->andWhere('pc.chart IN (
                            SELECT c FROM VideoGamesRecords\CoreBundle\Entity\Chart c
                            join c.group g
                        WHERE g.game = :game)')
            ->setParameter('game', $game);

        $query->getQuery()->execute();
    }

    /**
     * @param null    $idGame
     * @param null    $idGroup
     * @param integer $limit
     * @return mixed
     */
    public function rssTopScore($idGame = null, $idGroup = null, $limit = 20)
    {
        $query = $this->createQueryBuilder('pc')
            ->innerJoin('pc.chart', 'chart')
            ->innerJoin('chart.group', 'grp')
            ->innerJoin('grp.game', 'game')
            ->innerJoin('pc.player', 'player')
            ->where('pc.rank = 1')
            ->orderBy('pc.lastUpdate', 'DESC')
            ->setMaxResults($limit);
        if ($idGame != null) {
            $query->andWhere('game.id = :idGame')
                ->setParameter('idGame', $idGame);
        } elseif ($idGroup != null) {
            $query->andWhere('grp.id = :idGroup')
                ->setParameter('idGroup', $idGroup);
        }
        return $query->getQuery()->getResult();
    }
}
