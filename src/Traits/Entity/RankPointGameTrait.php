<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankPointGameTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $rankPointGame = 0;

    public function setRankPointGame(int $rankPointGame): void
    {
        $this->rankPointGame = $rankPointGame;
    }

    public function getRankPointGame(): int
    {
        return $this->rankPointGame;
    }
}
