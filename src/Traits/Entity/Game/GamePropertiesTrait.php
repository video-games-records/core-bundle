<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Game;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Entity\Game;

trait GamePropertiesTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false)
     * })
     */
    private Game $game;
}
