<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ChartRank5Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $chartRank5 = 0;

    public function setChartRank5(int $chartRank5): void
    {
        $this->chartRank5 = $chartRank5;
    }

    public function getChartRank5(): int
    {
        return $this->chartRank5;
    }
}
