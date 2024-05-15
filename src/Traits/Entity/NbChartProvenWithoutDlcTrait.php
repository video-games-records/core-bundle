<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbChartProvenWithoutDlcTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbChartProvenWithoutDlc = 0;

    public function setNbChartProvenWithoutDlc(int $nbChartProvenWithoutDlc): void
    {
        $this->nbChartProvenWithoutDlc = $nbChartProvenWithoutDlc;
    }

    public function getNbChartProvenWithoutDlc(): int
    {
        return $this->nbChartProvenWithoutDlc;
    }
}
