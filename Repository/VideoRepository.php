<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\Video;

class VideoRepository extends EntityRepository
{
    /**
     * Requires only video OK
     *
     * @param \Doctrine\ORM\QueryBuilder $query
     */
    private function onlyOK(QueryBuilder $query)
    {
        $query
            ->andWhere('v.status = :status')
            ->setParameter('status', Video::STATUS_OK);
    }
}
