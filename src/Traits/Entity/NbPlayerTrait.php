<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbPlayerTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbPlayer = 0;

    public function setNbPlayer(int $nbPlayer): void
    {
        $this->nbPlayer = $nbPlayer;
    }

    public function getNbPlayer(): int
    {
        return $this->nbPlayer;
    }
}
