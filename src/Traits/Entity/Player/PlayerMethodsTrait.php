<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Player;

use VideoGamesRecords\CoreBundle\Entity\Player;

trait PlayerMethodsTrait
{
    /**
     * @param Player $player
     * @return $this
     */
    public function setPlayer(Player $player): static
    {
        $this->player = $player;
        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
}
