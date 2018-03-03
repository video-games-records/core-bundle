<?php

namespace VideoGamesRecords\CoreBundle\Model\Player;

use Doctrine\ORM\Mapping as ORM;

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