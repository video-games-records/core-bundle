<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Handler;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use VideoGamesRecords\CoreBundle\Handler\ScoreInvestigationHandler;
use VideoGamesRecords\CoreBundle\Scheduler\Message\DesactivateScore;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DesactivateScoreHandler
{
    public function __construct(private readonly ScoreInvestigationHandler $handler)
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function __invoke(DesactivateScore $message): void
    {
        $this->handler->handle();
    }
}
