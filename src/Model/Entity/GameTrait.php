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
     * @param Game|null $game
     * @return $this
     */
    public function setGame(?Game $game = null): static
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
