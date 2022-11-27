<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use VideoGamesRecords\CoreBundle\Entity\Player;

trait PlayerTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id")
     * })
     */
    private Player $player;

    /**
     * Set player
     * @param Player|null $player
     */
    public function setPlayer(?Player $player = null): void
    {
        $this->player = $player;
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
