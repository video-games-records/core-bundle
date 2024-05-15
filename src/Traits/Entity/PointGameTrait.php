<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PointGameTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointGame = 0;

    public function getPointGame(): int
    {
        return $this->pointGame;
    }

    public function setPointGame(int $pointGame): void
    {
        $this->pointGame = $pointGame;
    }
}
