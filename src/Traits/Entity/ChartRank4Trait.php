<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ChartRank4Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $chartRank4 = 0;

    public function setChartRank4(int $chartRank4): void
    {
        $this->chartRank4 = $chartRank4;
    }

    public function getChartRank4(): int
    {
        return $this->chartRank4;
    }
}
