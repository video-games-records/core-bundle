<?php

namespace VideoGamesRecords\CoreBundle\Model;

use VideoGamesRecords\CoreBundle\Entity\Game as GameEntity;

trait Game
{
    /**
     * @var GameEntity
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id")
     * })
     */
    private $game;

    /**
     * Set game
     *
     * @param GameEntity $game
     * @return $this
     */
    public function setGame(GameEntity $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return GameEntity
     */
    public function getGame()
    {
        return $this->game;
    }
}
