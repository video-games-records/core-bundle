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
     * @return \VideoGamesRecords\CoreBundle\Entity\PlayerChart
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFromUnique($idPlayer, $idChart)
    {
        $query = $this->createQueryBuilder('pc')
            ->where('pc.idPlayer = :idPlayer')
            ->setParameter('idPlayer', $idPlayer)
            ->andWhere('pc.idChart = :idChart')
            ->setParameter('idChart', $idChart);

        return $query->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param \VideoGamesRecords\CoreBundle\Entity\Chart $chart
     * @param \VideoGamesRecords\CoreBundle\Entity\Player $player
     * @param int $limit
     * @return array
     * @todo
     * => Join etat to keep only boolRanking = 1
     * => If idPlayer, search for the rank and display a range of -5 and +5
     */
    public function getRanking($chart, $player = null, $limit = 5)
    {
        $queryBuilder = $this->createQueryBuilder('pc');
        $queryBuilder
            ->innerJoin('pc.player', 'p')
            ->addSelect('p')
            ->where('pc.rank IS NOT NULL')
            ->andWhere('pc.idChart = :idChart')
            ->setParameter('idChart', $chart->getId());

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

        foreach ($chart->getLibs() as $lib) {
            $key = 'value_' . $lib->getIdLibChart();
            $alias = 'pcl_' . $lib->getIdLibChart();
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
     * @param int $idChart
     * @return array
     */
    public function maj($idChart)
    {
        /** @var \VideoGamesRecords\CoreBundle\Entity\Chart $chart */
        $chart = $this->_em->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($idChart);
        $ranking = $this->getRanking($chart);

        // @todo disabled post (Rank is null)

        //----- Return players id
        $players = array();

        //----- Array of pointChart
        $pointsChart = Ranking::arrayPointRecord(count($ranking));

        foreach ($ranking as $k => $row) {
            /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
            $playerChart = $row[0];
            //----- If equal
            if ($playerChart->getNbEqual() == 1) {
                $pointChart = $pointsChart[$playerChart->getRank()];
            } else {
                $pointChart = (int)(
                    array_sum(
                        array_slice(array_values($pointsChart), $playerChart->getRank() - 1, $playerChart->getNbEqual())
                    ) / $playerChart->getNbEqual()
                );
            }
            $playerChart->setPointChart($pointChart);

            $this->_em->persist($playerChart);
            $this->_em->flush($playerChart);

            $players[] = $playerChart->getIdPlayer();
        }

        $chart->setStatusPlayer(Chart::STATUS_NORMAL);
        $this->getEntityManager()->persist($chart);
        $this->getEntityManager()->flush();

        return $players;
    }


    /**
     *
     */
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

        /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
        foreach ($list as $playerChart) {
            $playerChart->setIdStatus(PlayerChartStatus::ID_STATUS_NOT_PROOVED);
        }
        $this->getEntityManager()->flush();
    }


    /**
     * @param array $params
     * @return array
     */
    public function getRows($params = [])
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
}
