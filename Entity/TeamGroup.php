<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TeamGroup
 *
 * @ORM\Table(name="vgr_team_group")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamGroupRepository")
 */
class TeamGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="pointChart", type="float", nullable=false)
     */
    private $pointChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankPointChart", type="integer", nullable=false)
     */
    private $rankPointChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankMedal", type="integer", nullable=false)
     */
    private $rankMedal;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank0", type="integer", nullable=false)
     */
    private $chartRank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank1", type="integer", nullable=false)
     */
    private $chartRank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank2", type="integer", nullable=false)
     */
    private $chartRank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank3", type="integer", nullable=false)
     */
    private $chartRank3;

    /**
     * @var Team
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="id", nullable=false)
     * })
     */
    private $team;

    /**
     * @var Group
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGroup", referencedColumnName="id", nullable=false)
     * })
     */
    private $group;

    /**
     * Set pointChart
     * @param float $pointChart
     * @return $this
     */
    public function setPointChart(float $pointChart)
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
    public function setRankPointChart(int $rankPointChart)
    {
        $this->rankPointChart = $rankPointChart;
        return $this;
    }

    /**
     * Get rankPointChart
     *
     * @return integer
     */
    public function getRankPointChart()
    {
        return $this->rankPointChart;
    }

    /**
     * Set rankMedal
     * @param integer $rankMedal
     * @return $this
     */
    public function setRankMedal(int $rankMedal)
    {
        $this->rankMedal = $rankMedal;
        return $this;
    }

    /**
     * Get rankMedal
     *
     * @return integer
     */
    public function getRankMedal()
    {
        return $this->rankMedal;
    }

    /**
     * Set chartRank0
     * @param integer $chartRank0
     * @return $this
     */
    public function setChartRank0(int $chartRank0)
    {
        $this->chartRank0 = $chartRank0;
        return $this;
    }

    /**
     * Get chartRank0
     *
     * @return integer
     */
    public function getChartRank0()
    {
        return $this->chartRank0;
    }

    /**
     * Set chartRank1
     * @param integer $chartRank1
     * @return $this
     */
    public function setChartRank1(int $chartRank1)
    {
        $this->chartRank1 = $chartRank1;
        return $this;
    }

    /**
     * Get chartRank1
     *
     * @return integer
     */
    public function getChartRank1()
    {
        return $this->chartRank1;
    }

    /**
     * Set chartRank2
     * @param integer $chartRank2
     * @return $this
     */
    public function setChartRank2(int $chartRank2)
    {
        $this->chartRank2 = $chartRank2;
        return $this;
    }

    /**
     * Get chartRank2
     *
     * @return integer
     */
    public function getChartRank2()
    {
        return $this->chartRank2;
    }

    /**
     * Set chartRank3
     * @param integer $chartRank3
     * @return $this
     */
    public function setChartRank3(int $chartRank3)
    {
        $this->chartRank3 = $chartRank3;
        return $this;
    }

    /**
     * Get chartRank3
     *
     * @return integer
     */
    public function getChartRank3()
    {
        return $this->chartRank3;
    }

    /**
     * Set group
     * @param Group|null $group
     * @return $this
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }


    /**
     * Set team
     * @param Team|null $team
     * @return $this
     */
    public function setTeam(Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
