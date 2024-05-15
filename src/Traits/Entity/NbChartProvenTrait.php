<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbChartProvenTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbChartProven = 0;

    public function setNbChartProven(int $nbChartProven): void
    {
        $this->nbChartProven = $nbChartProven;
    }

    public function getNbChartProven(): int
    {
        return $this->nbChartProven;
    }
}
