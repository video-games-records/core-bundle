<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PointChartTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointChart = 0;

    public function setPointChart(int $pointChart): void
    {
        $this->pointChart = $pointChart;
    }

    public function getPointChart(): int
    {
        return $this->pointChart;
    }
}
