<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait GameRank3Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $gameRank3 = 0;

    public function setGameRank3(int $gameRank3): void
    {
        $this->gameRank3 = $gameRank3;
    }

    public function getGameRank3(): int
    {
        return $this->gameRank3;
    }
}
