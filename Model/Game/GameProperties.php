<?php

namespace VideoGamesRecords\CoreBundle\Model\Game;

use Doctrine\ORM\Mapping as ORM;

trait GameProperties
{
    /**
     * @ORM\Column(name="idGame", type="integer")
     */
    private $idGame;

    /**
     * @var Game
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id")
     * })
     */
    private $game;
}
