<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait AverageGameRankTrait
{
    #[ORM\Column(nullable: true)]
    private ?float $averageGameRank;

    public function setAverageGameRank(float $averageGameRank): void
    {
        $this->averageGameRank = $averageGameRank;
    }

    public function getAverageGameRank(): ?float
    {
        return $this->averageGameRank;
    }
}
