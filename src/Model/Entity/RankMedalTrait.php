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
     * @return $this
     */
    public function setRankMedal(int $rankMedal): static
    {
        $this->rankMedal = $rankMedal;
        return $this;
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
     * @return $this
     */
    public function setChartRank0(int $chartRank0): static
    {
        $this->chartRank0 = $chartRank0;
        return $this;
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
     * @return $this
     */
    public function setChartRank1(int $chartRank1): static
    {
        $this->chartRank1 = $chartRank1;
        return $this;
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
     * @return $this
     */
    public function setChartRank2(int $chartRank2): static
    {
        $this->chartRank2 = $chartRank2;
        return $this;
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
     * @return $this
     */
    public function setChartRank3(int $chartRank3): static
    {
        $this->chartRank3 = $chartRank3;
        return $this;
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
     * @return $this
     */
    public function setChartRank4(int $chartRank4): static
    {
        $this->chartRank4 = $chartRank4;
        return $this;
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
     * @return $this
     */
    public function setChartRank5(int $chartRank5): static
    {
        $this->chartRank5 = $chartRank5;
        return $this;
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

