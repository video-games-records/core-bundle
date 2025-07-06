<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Player;

readonly class UpdatePlayerSerieRank
{
    public function __construct(
        private int $serieId,
    ) {
    }

    public function getSerieId(): int
    {
        return $this->serieId;
    }
}
