<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait GameRank1Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $gameRank1 = 0;

    public function setGameRank1(int $gameRank1): void
    {
        $this->gameRank1 = $gameRank1;
    }

    public function getGameRank1(): int
    {
        return $this->gameRank1;
    }
}
