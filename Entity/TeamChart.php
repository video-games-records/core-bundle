<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * TeamChart
 *
 * @ORM\Table(name="vgr_team_chart", indexes={@ORM\Index(name="idxIdChart", columns={"idChart"}), @ORM\Index(name="idxIdTeam", columns={"idTeam"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamChartRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TeamChart
{


    /**
     * @ORM\Column(name="idTeam", type="integer")
     * @ORM\Id
     */
    private $idTeam;

    /**
     * @ORM\Column(name="idChart", type="integer")
     * @ORM\Id
     */
    private $idChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChart", type="float", nullable=false)
     */
    private $pointChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer", nullable=false)
     */
    private $rank;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank0", type="integer", nullable=false)
     */
    private $rank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank1", type="integer", nullable=false)
     */
    private $rank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank2", type="integer", nullable=false)
     */
    private $rank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank3", type="integer", nullable=false)
     */
    private $rank3;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="idTeam")
     * })
     */
    private $team;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="idChart")
     * })
     */
    private $chart;


    /**
     * Set idTeam
     *
     * @param integer $idTeam
     * @return $this
     */
    public function setIdTeam($idTeam)
    {
        $this->idTeam = $idTeam;
        return $this;
    }

    /**
     * Get idTeam
     *
     * @return integer
     */
    public function getIdTeam()
    {
        return $this->idTeam;
    }


    /**
     * Set idChart
     *
     * @param integer $idChart
     * @return $this
     */
    public function setIdChart($idChart)
    {
        $this->idChart = $idChart;
        return $this;
    }

    /**
     * Get idChart
     *
     * @return integer
     */
    public function getIdChart()
    {
        return $this->idChart;
    }

    /**
     * Set pointChart
     *
     * @param float $pointChart
     * @return $this
     */
    public function setPointChart($pointChart)
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
     * Set rank
     *
     * @param integer $rank
     * @return $this
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set rank
     *
     * @param integer $rank0
     * @return $this
     */
    public function setRank0($rank0)
    {
        $this->rank0 = $rank0;
        return $this;
    }

    /**
     * Get rank0
     *
     * @return integer
     */
    public function getRank0()
    {
        return $this->rank0;
    }

    /**
     * Set rank1
     *
     * @param integer $rank1
     * @return $this
     */
    public function setRank1($rank1)
    {
        $this->rank1 = $rank1;
        return $this;
    }

    /**
     * Get rank1
     *
     * @return integer
     */
    public function getRank1()
    {
        return $this->rank1;
    }

    /**
     * Set rank2
     *
     * @param integer $rank2
     * @return $this
     */
    public function setRank2($rank2)
    {
        $this->rank2 = $rank2;
        return $this;
    }

    /**
     * Get rank2
     *
     * @return integer
     */
    public function getRank2()
    {
        return $this->rank2;
    }

    /**
     * Set rank3
     *
     * @param integer $rank3
     * @return $this
     */
    public function setRank3($rank3)
    {
        $this->rank3 = $rank3;
        return $this;
    }

    /**
     * Get rank3
     *
     * @return integer
     */
    public function getRank3()
    {
        return $this->rank3;
    }


    /**
     * Set chart
     *
     * @param Chart $chart
     * @return $this
     */
    public function setChart(Chart $chart = null)
    {
        $this->chart = $chart;
        $this->setIdChart($chart->getIdChart());
        return $this;
    }

    /**
     * Get chart
     *
     * @return Chart
     */
    public function getChart()
    {
        return $this->chart;
    }


    /**
     * Set team
     *
     * @param Team $team
     * @return $this
     */
    public function setTeam(Team $team = null)
    {
        $this->team = $team;
        $this->setIdTeam($team->getIdTeam());
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
