<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Player;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Entity\Player;

trait PlayerPropertiesTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private Player $player;
}
