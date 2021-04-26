<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PlayerChartStatusRepository extends EntityRepository
{
    /**
     * @param $player
     * @return int|mixed|string
     */
    public function getStatsFromPlayer($player)
    {
        $query = $this->_em->createQuery("
            SELECT
                 s,
                 COUNT(pc) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus s
            JOIN s.playerCharts pc
            WHERE pc.player = :player
            GROUP BY s.id");

        $query->setParameter('player', $player);
        return $query->getResult();
    }
}
