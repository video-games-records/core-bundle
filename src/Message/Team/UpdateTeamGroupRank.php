<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Team;

readonly class UpdateTeamGroupRank
{
    public function __construct(
        private int $groupId,
    ) {
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }
}
