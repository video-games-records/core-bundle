<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait GameRank0Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $gameRank0 = 0;

    public function setGameRank0(int $gameRank0): void
    {
        $this->gameRank0 = $gameRank0;
    }

    public function getGameRank0(): int
    {
        return $this->gameRank0;
    }
}
