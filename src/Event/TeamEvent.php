<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Team;

class TeamEvent extends Event
{
    protected Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function getGame(): Team
    {
        return $this->team;
    }
}
