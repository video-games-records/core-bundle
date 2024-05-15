<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbChartTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbChart = 0;

    public function setNbChart(int $nbChart): void
    {
        $this->nbChart = $nbChart;
    }

    public function getNbChart(): int
    {
        return $this->nbChart;
    }
}
