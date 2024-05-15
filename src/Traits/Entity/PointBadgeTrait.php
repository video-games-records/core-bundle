<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PointBadgeTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointBadge = 0;

    public function setPointBadge(int $pointBadge): void
    {
        $this->pointBadge = $pointBadge;
    }

    public function getPointBadge(): int
    {
        return $this->pointBadge;
    }
}
