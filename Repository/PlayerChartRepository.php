<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use DateInterval;
use Doctrine\Persistence\ManagerRegistry;
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

class PlayerChartRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerChart::class);
    }

    /**
     * @param int $idPlayer
     * @param int $idChart
     * @return PlayerChart
     * @throws NonUniqueResultException
     */
    public function getFromUnique(int $idPlayer, int $idChart): ?PlayerChart
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
    public function getRankingPoints(Chart $chart, $maxRank = null, $player = null, $team = null): array
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
     * @return int|mixed|string
     */
    public function getPlatforms(Chart $chart)
    {
        $query = $this->_em->createQuery("
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

    /**
     * Provides every playerChart for update purpose.
     *
     * @param Chart $chart
     *
     * @return array
     */
    public function getRankingForUpdate(Chart $chart): array
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
    public function getDisableRanking(Chart $chart): array
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
    private function getRankingBaseQuery(Chart $chart): QueryBuilder
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
    private function getRankingBaseQuery2(Chart $chart): QueryBuilder
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
    public function getDataRank(): array
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
    public function getNbPostDay(DateTime $date1, DateTime $date2): array
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
        //@todo MAJ statut chart to MAJ
        $query->getQuery()->execute();
    }
}
