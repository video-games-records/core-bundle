<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use Doctrine\DBAL\Exception;
use VideoGamesRecords\CoreBundle\Manager\LostPositionManager;
use VideoGamesRecords\CoreBundle\Scheduler\Message\PurgeLostPosition;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class PurgeLostPositionHandler
{
    public function __construct(private readonly LostPositionManager $manager)
    {
    }

    /**
     * @param PurgeLostPosition $message
     * @return void
     * @throws Exception
     */
    public function __invoke(PurgeLostPosition $message): void
    {
        $this->manager->purge();
    }
}
