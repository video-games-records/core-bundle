<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use VideoGamesRecords\CoreBundle\Ranking\Command\ScoringPlayerRankingHandler;
use VideoGamesRecords\CoreBundle\Scheduler\Message\UpdatePlayerChartRanking;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdatePlayerChartRankingHandler
{
    public function __construct(private readonly ScoringPlayerRankingHandler $handler)
    {
    }

    /**
     * @throws NonUniqueResultException|NoResultException
     */
    public function __invoke(UpdatePlayerChartRanking $message): void
    {
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);

        $lock = $factory->createLock('UpdatePlayerChartRankingHandler');

        if ($lock->acquire()) {
            $this->handler->handle();
            $lock->release();
        } else {
            echo "Process IS LOCKED\n";
        }
    }
}
