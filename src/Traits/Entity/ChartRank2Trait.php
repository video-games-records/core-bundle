<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ChartRank2Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $chartRank2 = 0;

    public function setChartRank2(int $chartRank2): void
    {
        $this->chartRank2 = $chartRank2;
    }

    public function getChartRank2(): int
    {
        return $this->chartRank2;
    }
}
