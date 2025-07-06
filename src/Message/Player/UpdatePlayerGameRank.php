<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Player;

readonly class UpdatePlayerGameRank
{
    public function __construct(
        private int $gameId,
    ) {
    }

    public function getGameId(): int
    {
        return $this->gameId;
    }
}
