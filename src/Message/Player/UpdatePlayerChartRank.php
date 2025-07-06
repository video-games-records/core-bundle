<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Player;

readonly class UpdatePlayerChartRank
{
    public function __construct(
        private int $chartId,
    ) {
    }

    public function getChartId(): int
    {
        return $this->chartId;
    }
}
