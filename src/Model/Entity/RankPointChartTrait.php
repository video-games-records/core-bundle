<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankPointChartTrait
{
    /**
     * @ORM\Column(name="rankPointChart", type="integer", nullable=false)
     */
    private ?int $rankPointChart = 0;

    /**
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private int $pointChart = 0;

    /**
     * @param int $rankPointChart
     * @return $this
     */
    public function setRankPointChart(int $rankPointChart): static
    {
        $this->rankPointChart = $rankPointChart;
        return $this;
    }

    /**
     * Get rankPointChart
     * @return integer
     */
    public function getRankPointChart(): int
    {
        return $this->rankPointChart;
    }


    /**
     * @param int $pointChart
     * @return $this
     */
    public function setPointChart(int $pointChart): static
    {
        $this->pointChart = $pointChart;
        return $this;
    }

    /**
     * Get pointChart
     * @return integer
     */
    public function getPointChart(): int
    {
        return $this->pointChart;
    }
}
