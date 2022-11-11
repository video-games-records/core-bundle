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
