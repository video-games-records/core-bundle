<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankMedalTrait
{
    /**
     * @ORM\Column(name="rankMedal", type="integer", nullable=false)
     */
    private int $rankMedal = 0;

    /**
     * @ORM\Column(name="chartRank0", type="integer", nullable=false)
     */
    private int $chartRank0 = 0;

    /**
     * @ORM\Column(name="chartRank1", type="integer", nullable=false)
     */
    private int $chartRank1 = 0;

    /**
     * @ORM\Column(name="chartRank2", type="integer", nullable=false)
     */
    private int $chartRank2 = 0;

    /**
     * @ORM\Column(name="chartRank3", type="integer", nullable=false)
     */
    private int $chartRank3 = 0;

    /**
     * @ORM\Column(name="chartRank4", type="integer", nullable=false)
     */
    private int $chartRank4 = 0;

    /**
     * @ORM\Column(name="chartRank5", type="integer", nullable=false)
     */
    private int $chartRank5 = 0;

    /**
     * @param int $rankMedal
     * @return void
     */
    public function setRankMedal(int $rankMedal): void
    {
        $this->rankMedal = $rankMedal;
    }

    /**
     * Get rankMedal
     * @return integer
     */
    public function getRankMedal(): int
    {
        return $this->rankMedal;
    }

    /**
     * @param int $chartRank0
     * @return void
     */
    public function setChartRank0(int $chartRank0): void
    {
        $this->chartRank0 = $chartRank0;
    }

    /**
     * Get chartRank0
     * @return integer
     */
    public function getChartRank0(): int
    {
        return $this->chartRank0;
    }

    /**
     * @param int $chartRank1
     * @return void
     */
    public function setChartRank1(int $chartRank1): void
    {
        $this->chartRank1 = $chartRank1;
    }

    /**
     * Get chartRank1
     * @return integer
     */
    public function getChartRank1(): int
    {
        return $this->chartRank1;
    }

    /**
     * @param int $chartRank2
     * @return void
     */
    public function setChartRank2(int $chartRank2): void
    {
        $this->chartRank2 = $chartRank2;
    }

    /**
     * Get chartRank2
     * @return integer
     */
    public function getChartRank2(): int
    {
        return $this->chartRank2;
    }

    /**
     * @param int $chartRank3
     * @return void
     */
    public function setChartRank3(int $chartRank3): void
    {
        $this->chartRank3 = $chartRank3;
    }

    /**
     * Get chartRank3
     * @return integer
     */
    public function getChartRank3(): int
    {
        return $this->chartRank3;
    }

    /**
     * @param int $chartRank4
     * @return void
     */
    public function setChartRank4(int $chartRank4): void
    {
        $this->chartRank4 = $chartRank4;
    }

    /**
     * Get chartRank4
     * @return integer
     */
    public function getChartRank4(): int
    {
        return $this->chartRank4;
    }

    /**
     * @param int $chartRank5
     * @return void
     */
    public function setChartRank5(int $chartRank5): void
    {
        $this->chartRank5 = $chartRank5;
    }

    /**
     * Get chartRank5
     * @return integer
     */
    public function getChartRank5(): int
    {
        return $this->chartRank5;
    }
}

