<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use Doctrine\DBAL\Exception;
use VideoGamesRecords\CoreBundle\Handler\Badge\PlayerBadgeHandler;
use VideoGamesRecords\CoreBundle\Scheduler\Message\UpdatePlayerChartRanking;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdatePlayerBadgeHandler
{
    public function __construct(private readonly PlayerBadgeHandler $handler)
    {
    }

    /**
     * @param UpdatePlayerChartRanking $message
     * @return void
     * @throws Exception
     */
    public function __invoke(UpdatePlayerChartRanking $message): void
    {
        $this->handler->handle();
    }
}
