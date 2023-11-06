<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use VideoGamesRecords\CoreBundle\Traits\Entity\NbEqualTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * TeamGame
 *
 * @ORM\Table(name="vgr_team_serie")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamSerieRepository")
 */
class TeamSerie
{
    use NbEqualTrait;

    /**
     * @ORM\Column(name="pointGame", type="integer", nullable=false)
     */
    private int $pointGame = 0;

    /**
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private int $pointChart = 0;

    /**
     * @ORM\Column(name="rankPointChart", type="integer", nullable=false)
     */
    private int $rankPointChart;

    /**
     * @ORM\Column(name="rankMedal", type="integer", nullable=false)
     */
    private int $rankMedal;

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
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team", inversedBy="teamGame")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Team $team;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Serie", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSerie", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Serie $serie;

    /**
     * Set pointGame
     * @param integer $pointGame
     * @return $this
     */
    public function setPointGame(int $pointGame): TeamSerie
    {
        $this->pointGame = $pointGame;
        return $this;
    }

    /**
     * Get pointGame
     *
     * @return integer
     */
    public function getPointGame(): int
    {
        return $this->pointGame;
    }

    /**
     * Set pointChart
     * @param int $pointChart
     * @return $this
     */
    public function setPointChart(int $pointChart): TeamSerie
    {
        $this->pointChart = $pointChart;
        return $this;
    }

    /**
     * Get pointChart
     *
     * @return integer
     */
    public function getPointChart(): int
    {
        return $this->pointChart;
    }

    /**
     * Set rankPointChart
     * @param integer $rankPointChart
     * @return $this
     */
    public function setRankPointChart(int $rankPointChart): TeamSerie
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
     * Set rankMedal
     * @param integer $rankMedal
     * @return $this
     */
    public function setRankMedal(int $rankMedal): TeamSerie
    {
        $this->rankMedal = $rankMedal;
        return $this;
    }

    /**
     * Get rankMedal
     *
     * @return integer
     */
    public function getRankMedal(): int
    {
        return $this->rankMedal;
    }

    /**
     * Set chartRank0
     * @param integer $chartRank0
     * @return $this
     */
    public function setChartRank0(int $chartRank0): TeamSerie
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
    public function setChartRank1(int $chartRank1): TeamSerie
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
    public function setChartRank2(int $chartRank2): TeamSerie
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
    public function setChartRank3(int $chartRank3): TeamSerie
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
     * Set serie
     * @param Serie $serie
     * @return $this
     */
    public function setSerie(Serie $serie): TeamSerie
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return Serie
     */
    public function getSerie(): Serie
    {
        return $this->serie;
    }


    /**
     * Set team
     * @param Team $team
     * @return $this
     */
    public function setTeam(Team $team): TeamSerie
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
