<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use VideoGamesRecords\CoreBundle\Ranking\Command\ScoringTeamRankingHandler;
use VideoGamesRecords\CoreBundle\Scheduler\Message\UpdateTeamChartRanking;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateTeamChartRankingHandler
{
    public function __construct(private readonly ScoringTeamRankingHandler $handler)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(UpdateTeamChartRanking $message): void
    {
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);

        $lock = $factory->createLock('UpdateTeamChartRankingHandler');

        if ($lock->acquire()) {
            $this->handler->handle();
            $lock->release();
        } else {
            echo "Process IS LOCKED\n";
        }
    }
}
