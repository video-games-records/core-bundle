<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankPointBadgeTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $rankBadge = 0;

    public function setRankBadge(int $rankBadge): void
    {
        $this->rankBadge = $rankBadge;
    }

    public function getRankBadge(): int
    {
        return $this->rankBadge;
    }
}
