<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ChartRank3Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $chartRank3 = 0;

    public function setChartRank3(int $chartRank3): void
    {
        $this->chartRank3 = $chartRank3;
    }

    public function getChartRank3(): int
    {
        return $this->chartRank3;
    }
}
