<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class LostPositionRepository extends EntityRepository
{
    /**
     * @param $player
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNbLostPosition($player)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)');
        $this->wherePlayer($qb, $player);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $player
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNbNewLostPosition($player)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)');
        $this->wherePlayer($qb, $player);
        $qb->andWhere('l.createdAt > :now')
            ->setParameter('now', $player->getLastDisplayLostPosition());
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws Exception
     */
    public function purge()
    {
        $sql = "DELETE vgr_lostposition
        FROM vgr_lostposition
            INNER JOIN vgr_player_chart ON vgr_lostposition.idPlayer = vgr_player_chart.idPlayer AND vgr_lostposition.idChart = vgr_player_chart.idChart
        WHERE (vgr_player_chart.rank <= vgr_lostposition.oldRank)
        OR (vgr_player_chart.rank =1 AND vgr_player_chart.nbEqual = 1 AND vgr_lostposition.oldRank = 0)";
        $this->_em->getConnection()->executeStatement($sql);
    }


    /**
     * @param QueryBuilder $query
     * @param              $player
     */
    private function wherePlayer(QueryBuilder $query, $player)
    {
        $query->where('l.player = :player')
            ->setParameter('player', $player);
    }

}
