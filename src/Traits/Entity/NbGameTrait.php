<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbGameTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbGame = 0;

    public function setNbGame(int $nbGame): void
    {
        $this->nbGame = $nbGame;
    }

    public function getNbGame(): int
    {
        return $this->nbGame;
    }
}
