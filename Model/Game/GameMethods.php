<?php

namespace VideoGamesRecords\CoreBundle\Model\Game;

use VideoGamesRecords\CoreBundle\Entity\Game;

trait GameMethods
{
    /**
     * Set idGame
     *
     * @param integer $idGame
     * @return $this
     */
    public function setIdGame($idGame)
    {
        $this->idGame = $idGame;
        return $this;
    }

    /**
     * Get idGame
     *
     * @return integer
     */
    public function getIdGame()
    {
        return $this->idGame;
    }

    /**
     * Set game
     *
     * @param Game $game
     * @return $this
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;
        if (null !== $game) {
            $this->setIdGame($game->getId());
        }

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }
}
