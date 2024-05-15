<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait NbChartWithoutDlcTrait
{
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbChartWithoutDlc = 0;

    public function setNbChartWithoutDlc(int $nbChartWithoutDlc): void
    {
        $this->nbChartWithoutDlc = $nbChartWithoutDlc;
    }

    public function getNbChartWithoutDlc(): int
    {
        return $this->nbChartWithoutDlc;
    }
}
