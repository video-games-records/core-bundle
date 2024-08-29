<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use Doctrine\DBAL\Exception;
use VideoGamesRecords\CoreBundle\Manager\LostPositionManager;
use VideoGamesRecords\CoreBundle\Scheduler\Message\DesactivateScore;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class PurgeLostPositionHandler
{
    public function __construct(private readonly LostPositionManager $manager)
    {
    }

    /**
     * @param DesactivateScore $message
     * @return void
     * @throws Exception
     */
    public function __invoke(DesactivateScore $message): void
    {
        $this->manager->purge();
    }
}
