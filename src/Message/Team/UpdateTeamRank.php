<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Team;

readonly class UpdateTeamRank
{
    public function getUniqueIdentifier(): string
    {
        return 'UpdateTeamRank';
    }
}
