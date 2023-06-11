<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class GameDayRepository extends EntityRepository
{
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
