<?php

namespace VideoGamesRecords\CoreBundle\Handler\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingCommandInterface;

abstract class AbstractRankingHandler implements RankingCommandInterface
{
    protected EntityManagerInterface $em;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle($mixed): void
    {

    }
}
