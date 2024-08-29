<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use Doctrine\ORM\NonUniqueResultException;
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
     * @throws NonUniqueResultException
     */
    public function __invoke(UpdatePlayerChartRanking $message): void
    {
        $this->handler->handle();
    }
}
