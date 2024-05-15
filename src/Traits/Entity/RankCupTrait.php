<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankCupTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $rankCup = 0;

    public function setRankCup(int $rankCup): void
    {
        $this->rankCup = $rankCup;
    }

    public function getRankCup(): int
    {
        return $this->rankCup;
    }
}
