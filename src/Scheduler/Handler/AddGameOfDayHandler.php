<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use VideoGamesRecords\CoreBundle\Manager\GameOfDayManager;
use VideoGamesRecords\CoreBundle\Scheduler\Message\AddGameOfDay;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddGameOfDayHandler
{
    public function __construct(private readonly GameOfDayManager $manager)
    {
    }

    public function __invoke(AddGameOfDay $message): void
    {
        $this->manager->addTomorrowGame();
    }
}
