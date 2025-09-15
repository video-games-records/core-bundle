<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Message;

/**
 * Message sent daily to check and generate rankings when needed
 */
class DailyRanking
{
    public function __construct(
        private ?\DateTime $date = null
    ) {
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }
}
