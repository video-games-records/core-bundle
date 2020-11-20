<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;

/**
 * TeamGame
 *
 * @ORM\Table(name="vgr_team_game")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamGameRepository")
 * @ApiFilter(SearchFilter::class, properties={"team": "exact"})
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *          "parameterName": "groups",
 *          "overrideDefaultGroups": true,
 *          "whitelist": {
 *              "game.read.mini",
 *              "teamGame.game",
 *              "teamGame.pointChart",
 *              "teamGame.medal"
 *          }
 *      }
 * )
 * @ApiFilter(OrderFilter::class, properties={"pointGame": "DESC"}, arguments={"orderParameterName"="order"})
 */
class TeamGame
{
    /**
     * @var integer
     *
     * @ORM\Column(name="pointGame", type="float", nullable=false)
     */
    private $pointGame = 0;

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
     * @var integer
     *
     * @ORM\Column(name="nbEqual", type="integer", nullable=false)
     */
    private $nbEqual = 0;

    /**
     * @var Team
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team", inversedBy="teamGame")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="id", nullable=false)
     * })
     */
    private $team;

    /**
     * @var Game
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false)
     * })
     */
    private $game;

    /**
     * Set pointGame
     * @param float $pointGame
     * @return $this
     */
    public function setPointGame(float $pointGame)
    {
        $this->pointGame = $pointGame;
        return $this;
    }

    /**
     * Get pointGame
     *
     * @return float
     */
    public function getPointGame()
    {
        return $this->pointGame;
    }

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
     * Set nbEqual
     * @param integer $nbEqual
     * @return $this
     */
    public function setNbEqual(int $nbEqual)
    {
        $this->nbEqual = $nbEqual;
        return $this;
    }

    /**
     * Get nbEqual
     *
     * @return integer
     */
    public function getNbEqual()
    {
        return $this->nbEqual;
    }

    /**
     * Set game
     * @param Game|null $game
     * @return $this
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
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
