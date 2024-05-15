<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Game;

use VideoGamesRecords\CoreBundle\Entity\Game;

trait GameMethodsTrait
{
    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }
}
