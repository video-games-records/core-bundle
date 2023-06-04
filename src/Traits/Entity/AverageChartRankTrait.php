<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait AverageChartRankTrait
{
    /**
     * @ORM\Column(name="averageChartRank", type="float", nullable=true)
     */
    private ?float $averageChartRank;

    /**
     * @param float $averageChartRank
     * @return $this
     */
    public function setAverageChartRank(float $averageChartRank): static
    {
        $this->averageChartRank = $averageChartRank;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAverageChartRank(): ?float
    {
        return $this->averageChartRank;
    }
}
