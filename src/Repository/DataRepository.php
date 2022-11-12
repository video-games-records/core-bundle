<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Data;

/**
 * Specific repository that serves the Data entity.
 */
class DataRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Data::class);
    }
}
