<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Player;

readonly class UpdatePlayerPlatformRank
{
    public function __construct(
        private int $platformId,
    ) {
    }

    public function getPlatformId(): int
    {
        return $this->platformId;
    }

    public function getUniqueIdentifier(): string
    {
        return 'UpdatePlayerPlatformRank' . $this->platformId;
    }
}
