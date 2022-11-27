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
     * @return void
     */
    public function setRankPointChart(int $rankPointChart): void
    {
        $this->rankPointChart = $rankPointChart;
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
     * @return void
     */
    public function setPointChart(int $pointChart): void
    {
        $this->pointChart = $pointChart;
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
