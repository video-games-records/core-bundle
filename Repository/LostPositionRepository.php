<?php

namespace VideoGamesRecords\CoreBundle\Repository;

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
            ->setParameter('now', new \DateTime());
        return $qb->getQuery()->getSingleScalarResult();
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
