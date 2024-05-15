<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ChartRank0Trait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $chartRank0 = 0;

    public function setChartRank0(int $chartRank0): void
    {
        $this->chartRank0 = $chartRank0;
    }

    public function getChartRank0(): int
    {
        return $this->chartRank0;
    }
}
