<?php

namespace VideoGamesRecords\CoreBundle\Model;

use VideoGamesRecords\CoreBundle\Entity\Player as PlayerEntity;

trait Player
{
    /**
     * @var PlayerEntity
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id")
     * })
     */
    private $player;

    /**
     * Set player
     * @param PlayerEntity|object|null $player
     * @return $this
     */
    public function setPlayer(PlayerEntity $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return PlayerEntity
     */
    public function getPlayer()
    {
        return $this->player;
    }
}
