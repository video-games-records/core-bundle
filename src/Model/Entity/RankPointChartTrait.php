<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankPointChartTrait
{
    /**
     * @ORM\Column(name="rankPointChart", type="integer", nullable=false, options={"default" : 0})
     */
    private int $rankPointChart;

    /**
     * @ORM\Column(name="pointChart", type="integer", nullable=false, options={"default" : 0})
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
     * @return int
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
