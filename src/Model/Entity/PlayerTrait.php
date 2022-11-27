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
     * @param Player|null $player
     * @return $this
     */
    public function setPlayer(?Player $player = null): static
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
