<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Team;

readonly class UpdateTeamGameRank
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
