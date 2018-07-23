<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class PlayerChartRepository extends EntityRepository
{
    /**
     * @param int $idPlayer
     * @param int $idChart
     *
     * @return \VideoGamesRecords\CoreBundle\Entity\PlayerChart
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFromUnique($idPlayer, $idChart)
    {
        $query = $this->createQueryBuilder('pc')
            ->join('pc.player', 'p')
            ->join('pc.chart', 'c')
            ->where('p.idPlayer = :idPlayer')
            ->setParameter('idPlayer', $idPlayer)
            ->andWhere('c.id = :idChart')
            ->setParameter('idChart', $idChart);

        return $query->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $idChart
     *
     * @return array
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function maj($idChart)
    {
        /** @var \VideoGamesRecords\CoreBundle\Entity\Chart $chart */
        $chart       = $this->_em->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($idChart);
        $nbLib       = $chart->getLibs()->count();
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
            /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
            $playerChart = $item[0];
            $players[]   = $playerChart->getPlayer()->getIdPlayer();
            $playerChart
                ->setTopScore(false);

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

            $previousLibValue = $libValue;
        }

        $chart->setStatusPlayer(Chart::STATUS_NORMAL);
        //$this->getEntityManager()->persist($chart);
        $this->getEntityManager()->flush();

        return $players;
    }

    /**
     * Provides every playerChart for update purpose.
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\Chart $chart
     *
     * @return array
     */
    public function getRankingForUpdate(Chart $chart)
    {
        $queryBuilder = $this->createQueryBuilder('pc');
        $queryBuilder
            ->innerJoin('pc.player', 'p')
            ->addSelect('p')
            ->innerJoin('pc.status', 'status')
            ->addSelect('status')
            ->where('pc.chart = :chart')
            ->setParameter('chart', $chart)
            ->andWhere('status.boolRanking = 1');

        foreach ($chart->getLibs() as $lib) {
            $key             = 'value_' . $lib->getIdLibChart();
            $alias           = 'pcl_' . $lib->getIdLibChart();
            $subQueryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select(sprintf('%s.value', $alias))
                ->from('VideoGamesRecordsCoreBundle:PlayerChartLib', $alias)
                ->where(sprintf('%s.libChart = :%s', $alias, $key))
                ->andWhere(sprintf('%s.player = pc.player', $alias))
                ->setParameter($key, $lib);

            $queryBuilder
                ->addSelect(sprintf('(%s) as %s', $subQueryBuilder->getQuery()->getDQL(), $key))
                ->addOrderBy($key, $lib->getType()->getOrderBy())
                ->setParameter($key, $lib);
        }
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Provides valid ranking.
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\Chart $chart
     * @param \VideoGamesRecords\CoreBundle\Entity\Player $player
     * @param int|null $limit
     *
     * @return array
     * @todo
     * => If idPlayer, search for the rank and display a range of -5 and +5
     */
    public function getRanking(Chart $chart, $player = null, $limit = null)
    {
        $queryBuilder = $this->getRankingBaseQuery($chart);
        $queryBuilder
            ->andWhere('status.boolRanking = 1');

        if (null !== $limit && null !== $player) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->orX('pc.rank <= :maxRank', 'pc.player = :player')
                )
                ->setParameter('maxRank', $limit)
                ->setParameter('player', $player);
        } elseif (null !== $limit) {
            $queryBuilder
                ->andWhere('pc.rank <= :maxRank')
                ->setParameter('maxRank', $limit);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param \VideoGamesRecords\CoreBundle\Entity\Chart $chart
     *
     * @return \Doctrine\ORM\QueryBuilder
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
            ->orderBy('pc.rank');

        foreach ($chart->getLibs() as $lib) {
            $key             = 'value_' . $lib->getIdLibChart();
            $alias           = 'pcl_' . $lib->getIdLibChart();
            $subQueryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select(sprintf('%s.value', $alias))
                ->from('VideoGamesRecordsCoreBundle:PlayerChartLib', $alias)
                ->where(sprintf('%s.libChart = :%s', $alias, $key))
                ->andWhere(sprintf('%s.player = pc.player', $alias))
                ->setParameter($key, $lib);

            $queryBuilder
                ->addSelect(sprintf('(%s) as %s', $subQueryBuilder->getQuery()->getDQL(), $key))
                ->setParameter($key, $lib);
        }

        return $queryBuilder;
    }

    /**
     * Provides disabled list.
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\Chart $chart
     *
     * @return array
     */
    public function getDisableRanking(Chart $chart)
    {
        $queryBuilder = $this->getRankingBaseQuery($chart);
        $queryBuilder
            ->andWhere('status.boolRanking = 0');

        return $queryBuilder->getQuery()->getResult();
    }

    public function majInvestigation()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P14D'));

        $query = $this->createQueryBuilder('pc')
            ->where('pc.idStatus = :idStatus')
            ->setParameter('idStatus', PlayerChartStatus::ID_STATUS_INVESTIGATION)
            ->andWhere('pc.dateInvestigation < :date')
            ->setParameter('date', $date->format('Y-m-d'));

        $list = $query->getQuery()->getResult();

        $statusReference = $this->_em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NOT_PROOVED);
        /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
        foreach ($list as $playerChart) {
            $playerChart->setStatus($statusReference);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getRows(array $params = [])
    {
        $query = $this->createQueryBuilder('pc');

        if (array_key_exists('idPlayer', $params)) {
            $query->where('pc.idPlayer= :idPlayer')
                ->setParameter('idPlayer', $params['idPlayer']);
        }

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        if (array_key_exists('orderBy', $params)) {
            $query->orderBy($params['orderBy']['column'], $params['orderBy']['order']);
        }

        return $query->getQuery()->getResult();
    }


    /**
     * @return array
     */
    public function getDataRank()
    {
        $query = $this->_em->createQuery("
                    SELECT
                         pc.idPlayer,
                         CASE WHEN pc.rank > 29 THEN 30 ELSE pc.rank AS rank,
                         COUNT(pc.idPlayerChart) as nb
                    FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
                    WHERE pc.rank > 3            
                    GROUP BY pc.idPlayer, rank");

        $result = $query->getResult();
        $data = array();
        foreach ($result as $row) {
            $data[$row['idPlayer']][$row['rank']] = $row['nb'];
        }
        return $data;
    }
}
