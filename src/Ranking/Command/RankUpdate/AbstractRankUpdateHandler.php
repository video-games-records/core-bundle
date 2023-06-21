<?php

namespace VideoGamesRecords\CoreBundle\Ranking\Command\RankUpdate;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankUpdateInterface;

abstract class AbstractRankUpdateHandler implements RankUpdateInterface
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(): void
    {

    }
}
