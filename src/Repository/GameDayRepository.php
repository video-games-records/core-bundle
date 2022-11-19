<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\GameDay;

class GameDayRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameDay::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getMaxDay(): mixed
    {
        $qb = $this->createQueryBuilder('gd')
            ->select('max(gd.day)');
        $result = $qb->getQuery()->getSingleResult();
        return $result[1];
    }
}
