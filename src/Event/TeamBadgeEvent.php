<?php

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\TeamBadge;

class TeamBadgeEvent extends Event
{
    protected TeamBadge $teamBadge;

    public function __construct(TeamBadge $teamBadge)
    {
        $this->teamBadge = $teamBadge;
    }

    public function getTeamBadge(): TeamBadge
    {
        return $this->teamBadge;
    }
}
