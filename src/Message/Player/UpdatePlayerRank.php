<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Player;

readonly class UpdatePlayerRank
{
    public function getUniqueIdentifier(): string
    {
        return 'UpdatePlayerRank';
    }
}
