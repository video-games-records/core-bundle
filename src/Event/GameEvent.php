<?php

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameEvent extends Event
{
    protected Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }
}
