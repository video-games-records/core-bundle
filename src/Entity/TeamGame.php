<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbEqualTrait;

/**
 * TeamGame
 *
 * @ORM\Table(name="vgr_team_game")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamGameRepository")
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "team": "exact",
 *          "game": "exact",
 *          "game.badge": "exact",
 *     }
 * )
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *          "parameterName": "groups",
 *          "overrideDefaultGroups": true,
 *          "whitelist": {
 *              "game.read.mini",
 *              "game.platforms",
 *              "platform.read",
 *              "teamGame.game",
 *              "teamGame.pointChart",
 *              "teamGame.medal",
 *              "game.stats",
 *          }
 *      }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "rankPointChart": "ASC",
 *          "chartRank0": "DESC",
 *          "chartRank1": "DESC",
 *          "chartRank2": "DESC",
 *          "chartRank3": "DESC",
 *          "pointGame": "DESC",
 *          "nbEqual": "ASC",
 *          "game.libGameEn" : "ASC",
 *          "game.libGameFr" : "ASC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class TeamGame
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
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Game $game;

    /**
     * Set pointGame
     * @param integer $pointGame
     * @return $this
     */
    public function setPointGame(int $pointGame): TeamGame
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
     * @param float $pointChart
     * @return $this
     */
    public function setPointChart(float $pointChart): TeamGame
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
    public function setRankPointChart(int $rankPointChart): TeamGame
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
    public function setRankMedal(int $rankMedal): TeamGame
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
    public function setChartRank0(int $chartRank0): TeamGame
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
    public function setChartRank1(int $chartRank1): TeamGame
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
    public function setChartRank2(int $chartRank2): TeamGame
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
    public function setChartRank3(int $chartRank3): TeamGame
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
     * Set game
     * @param Game $game
     * @return $this
     */
    public function setGame(Game $game): TeamGame
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }


    /**
     * Set team
     * @param Team $team
     * @return $this
     */
    public function setTeam(Team $team): TeamGame
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
