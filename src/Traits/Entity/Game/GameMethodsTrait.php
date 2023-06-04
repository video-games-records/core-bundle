<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Game;

use VideoGamesRecords\CoreBundle\Entity\Game;

trait GameMethodsTrait
{
    /**
     * @param Game $game
     * @return $this
     */
    public function setGame(Game $game): static
    {
        $this->game = $game;
        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }
}
