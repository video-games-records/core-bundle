<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TeamChart
 *
 * @ORM\Table(name="vgr_team_chart")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamChartRepository")
 */
class TeamChart
{
    /**
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private int $pointChart = 0;

    /**
     * @ORM\Column(name="rankPointChart", type="integer", nullable=false)
     */
    private int $rankPointChart;

    /**
     * @ORM\Column(name="chartRank0", type="integer", nullable=false)
     */
    private int $chartRank0;

    /**
     * @ORM\Column(name="chartRank1", type="integer", nullable=false)
     */
    private int $chartRank1;

    /**
     * @ORM\Column(name="chartRank2", type="integer", nullable=false)
     */
    private int $chartRank2;

    /**
     * @ORM\Column(name="chartRank3", type="integer", nullable=false)
     */
    private int $chartRank3;

    /**
     * @var Team
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="id", nullable=false)
     * })
     */
    private Team $team;

    /**
     * @var Chart
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id", nullable=false)
     * })
     */
    private Chart $chart;

    /**
     * Set pointChart
     * @param float $pointChart
     * @return $this
     */
    public function setPointChart(float $pointChart): Self
    {
        $this->pointChart = $pointChart;
        return $this;
    }

    /**
     * Get pointChart
     *
     * @return float
     */
    public function getPointChart()
    {
        return $this->pointChart;
    }

    /**
     * Set rankPointChart
     * @param integer $rankPointChart
     * @return $this
     */
    public function setRankPointChart(int $rankPointChart): Self
    {
        $this->rankPointChart = $rankPointChart;
        return $this;
    }

    /**
     * Get rankPointChart
     *
     * @return integer
     */
    public function getRankPointChart(): int
    {
        return $this->rankPointChart;
    }

    /**
     * Set chartRank0
     * @param integer $chartRank0
     * @return $this
     */
    public function setChartRank0(int $chartRank0): Self
    {
        $this->chartRank0 = $chartRank0;
        return $this;
    }

    /**
     * Get chartRank0
     *
     * @return integer
     */
    public function getChartRank0(): int
    {
        return $this->chartRank0;
    }

    /**
     * Set chartRank1
     * @param integer $chartRank1
     * @return $this
     */
    public function setChartRank1(int $chartRank1): Self
    {
        $this->chartRank1 = $chartRank1;
        return $this;
    }

    /**
     * Get chartRank1
     *
     * @return integer
     */
    public function getChartRank1(): int
    {
        return $this->chartRank1;
    }

    /**
     * Set chartRank2
     * @param integer $chartRank2
     * @return $this
     */
    public function setChartRank2(int $chartRank2): Self
    {
        $this->chartRank2 = $chartRank2;
        return $this;
    }

    /**
     * Get chartRank2
     *
     * @return integer
     */
    public function getChartRank2(): int
    {
        return $this->chartRank2;
    }

    /**
     * Set chartRank3
     * @param integer $chartRank3
     * @return $this
     */
    public function setChartRank3(int $chartRank3): Self
    {
        $this->chartRank3 = $chartRank3;
        return $this;
    }

    /**
     * Get chartRank3
     *
     * @return integer
     */
    public function getChartRank3(): int
    {
        return $this->chartRank3;
    }

    /**
     * Set chart
     * @param Chart $chart
     * @return $this
     */
    public function setChart(Chart $chart): Self
    {
        $this->chart = $chart;

        return $this;
    }

    /**
     * Get chart
     *
     * @return Chart
     */
    public function getChart(): Chart
    {
        return $this->chart;
    }


    /**
     * Set team
     * @param Team|null $team
     * @return $this
     */
    public function setTeam(Team $team = null): Self
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }
}
