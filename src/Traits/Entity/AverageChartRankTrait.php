<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait AverageChartRankTrait
{
    #[ORM\Column(nullable: true)]
    private ?float $averageChartRank;

    public function setAverageChartRank(float $averageChartRank): void
    {
        $this->averageChartRank = $averageChartRank;
    }

    public function getAverageChartRank(): ?float
    {
        return $this->averageChartRank;
    }
}
