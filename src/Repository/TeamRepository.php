<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * TeamRepository
 */
class TeamRepository extends EntityRepository
{
    /**
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function getStats(): mixed
    {
        $qb = $this->createQueryBuilder('team')
            ->select('COUNT(team.id)');
        $qb->where('team.pointChart > 0');

        return $qb->getQuery()
            ->getOneOrNullResult();
    }
}
