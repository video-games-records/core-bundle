<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\TeamGame;

/**
 * TeamGameRepository
 */
class TeamGameRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamGame::class);
    }
}
