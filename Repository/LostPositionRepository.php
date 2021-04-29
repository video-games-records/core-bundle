<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class LostPositionRepository extends EntityRepository
{
    /**
     * @param $player
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNbLostPosition($player)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)');
        $this->wherePlayer($qb, $player);
        return $qb->getQuery()->getSingleScalarResult();
    }


    /**
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param                            $player
     */
    private function wherePlayer(QueryBuilder $query, $player)
    {
        $query->where('l.player = :player')
            ->setParameter('player', $player);
    }
}
