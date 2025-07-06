<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Player;

readonly class UpdatePlayerCountryRank
{
    public function __construct(
        private int $countryId,
    ) {
    }

    public function getCountryId(): int
    {
        return $this->countryId;
    }
}
