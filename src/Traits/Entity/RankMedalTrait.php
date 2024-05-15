<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankMedalTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $rankMedal = 0;

    public function setRankMedal(int $rankMedal): void
    {
        $this->rankMedal = $rankMedal;
    }

    public function getRankMedal(): int
    {
        return $this->rankMedal;
    }
}
