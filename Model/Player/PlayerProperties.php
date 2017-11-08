<?php

namespace VideoGamesRecords\CoreBundle\Model\Player;

trait PlayerProperties
{
    /**
     * @ORM\Column(name="idPlayer", type="integer")
     */
    private $idPlayer;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="idPlayer")
     * })
     */
    private $player;
}
