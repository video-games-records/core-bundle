<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use VideoGamesRecords\CoreBundle\Entity\Game;

trait GameTrait
{
    /**
     * @var Game
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id")
     * })
     */
    private Game $game;

    /**
     * Set game
     * @param Game|null $game
     */
    public function setGame(?Game $game = null): void
    {
        $this->game = $game;
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
