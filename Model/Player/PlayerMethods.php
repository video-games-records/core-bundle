<?php

namespace VideoGamesRecords\CoreBundle\Model\Player;

use VideoGamesRecords\CoreBundle\Entity\Player;

trait PlayerMethods
{
    /**
     * Set idPlayer
     *
     * @param integer $idPlayer
     * @return $this
     */
    public function setIdPlayer($idPlayer)
    {
        $this->idPlayer = $idPlayer;
        return $this;
    }

    /**
     * Get idPlayer
     *
     * @return integer
     */
    public function getIdPlayer()
    {
        return $this->idPlayer;
    }

    /**
     * Set player
     *
     * @param Player $player
     * @return $this
     */
    public function setPlayer(Player $player = null)
    {
        $this->player = $player;
        if (null !== $player) {
            $this->setIdPlayer($player->getIdPlayer());
        }

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }
}
