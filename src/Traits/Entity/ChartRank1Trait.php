<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ChartRank1Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $chartRank1 = 0;

    public function setChartRank1(int $chartRank1): void
    {
        $this->chartRank1 = $chartRank1;
    }

    public function getChartRank1(): int
    {
        return $this->chartRank1;
    }
}
