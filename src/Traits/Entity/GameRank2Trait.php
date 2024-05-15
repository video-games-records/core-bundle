<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait GameRank2Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $gameRank2 = 0;

    public function setGameRank2(int $gameRank2): void
    {
        $this->gameRank2 = $gameRank2;
    }

    public function getGameRank2(): int
    {
        return $this->gameRank2;
    }
}
