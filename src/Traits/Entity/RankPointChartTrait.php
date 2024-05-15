<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankPointChartTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $rankPointChart = 0;


    public function setRankPointChart(int $rankPointChart): void
    {
        $this->rankPointChart = $rankPointChart;
    }

    public function getRankPointChart(): int
    {
        return $this->rankPointChart;
    }
}
