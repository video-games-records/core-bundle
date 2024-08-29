<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler\Message;

class UpdatePlayerChartRanking
{
    private ?int $nb;

    public function getNb(): ?int
    {
        return $this->nb;
    }
}
