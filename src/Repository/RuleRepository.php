<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Rule;

class RuleRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rule::class);
    }
}
