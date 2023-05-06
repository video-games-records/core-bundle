<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Model\Entity\NbChartTrait;

/**
 * PlayerGame
 *
 * @ORM\Table(name="vgr_player_game")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerGameRepository")
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "player": "exact",
 *          "game": "exact",
 *          "game.platforms": "exact",
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
 *              "playerGame.game",
 *              "playerGame.pointChart",
 *              "playerGame.medal",
 *              "playerGame.proof",
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
 *          "nbChart": "DESC",
 *          "nbEqual": "ASC",
 *          "game.nbPlayer" : "DESC",
 *          "game.libGameEn" : "ASC",
 *          "game.libGameFr" : "ASC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class PlayerGame
{
    use NbChartTrait;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Player $player;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Game $game;

    /**
     * @ORM\Column(name="rankPointChart", type="integer", nullable=false)
     */
    private int $rankPointChart = 0;

    /**
     * @ORM\Column(name="rankMedal", type="integer", nullable=false)
     */
    private int $rankMedal = 0;

    /**
     * @var integer
     *
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
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private int $pointChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChartWithoutDlc", type="integer", nullable=false)
     */
    private int $pointChartWithoutDlc = 0;

    /**
     * @ORM\Column(name="nbChartProven", type="integer", nullable=false)
     */
    private int $nbChartProven = 0;

    /**
     * @ORM\Column(name="nbChartWithoutDlc", type="integer", nullable=false)
     */
    private int $nbChartWithoutDlc = 0;

    /**
     * @ORM\Column(name="nbChartProvenWithoutDlc", type="integer", nullable=false)
     */
    private int $nbChartProvenWithoutDlc = 0;

    /**
     * @ORM\Column(name="pointGame", type="integer", nullable=false)
     */
    private int $pointGame = 0;

    /**
     * @ORM\Column(name="nbEqual", type="integer", nullable=false, options={"default" : 1})
     */
    private int $nbEqual = 1;

    /**
     * @ORM\Column(name="lastUpdate", type="datetime", nullable=false)
     */
    private DateTime $lastUpdate;


    private $statuses;

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
     * Set rankMedal
     * @param integer $rankMedal
     * @return $this
     */
    public function setRankMedal(int $rankMedal): Self
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
     * Set chartRank4
     * @param integer $chartRank4
     * @return $this
     */
    public function setChartRank4(int $chartRank4): Self
    {
        $this->chartRank4 = $chartRank4;
        return $this;
    }

    /**
     * Get chartRank4
     *
     * @return integer
     */
    public function getChartRank4(): int
    {
        return $this->chartRank4;
    }

    /**
     * Set chartRank5
     * @param integer $chartRank5
     * @return $this
     */
    public function setChartRank5(int $chartRank5): Self
    {
        $this->chartRank5 = $chartRank5;
        return $this;
    }

    /**
     * Get chartRank5
     *
     * @return integer
     */
    public function getChartRank5(): int
    {
        return $this->chartRank5;
    }

    /**
     * Set pointChart
     * @param integer $pointChart
     * @return $this
     */
    public function setPointChart(int $pointChart): Self
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
     * Set pointChartWithoutDlc
     * @param integer $pointChartWithoutDlc
     * @return $this
     */
    public function setPointChartWithoutDlc(int $pointChartWithoutDlc): Self
    {
        $this->pointChartWithoutDlc = $pointChartWithoutDlc;
        return $this;
    }

    /**
     * Get pointChartWithoutDlc
     *
     * @return integer
     */
    public function getPointChartWithoutDlc(): int
    {
        return $this->pointChartWithoutDlc;
    }


    /**
     * Set nbChartProven
     * @param integer $nbChartProven
     * @return $this
     */
    public function setNbChartProven(int $nbChartProven): Self
    {
        $this->nbChartProven = $nbChartProven;
        return $this;
    }

    /**
     * Get nbChartProven
     *
     * @return integer
     */
    public function getNbChartProven(): int
    {
        return $this->nbChartProven;
    }

    /**
     * Set nbChartWithoutDlc
     * @param integer $nbChartWithoutDlc
     * @return $this
     */
    public function setNbChartWithoutDlc(int $nbChartWithoutDlc): Self
    {
        $this->nbChartWithoutDlc = $nbChartWithoutDlc;
        return $this;
    }

    /**
     * Get nbChartWithoutDlc
     *
     * @return integer
     */
    public function getNbChartWithoutDlc(): int
    {
        return $this->nbChartWithoutDlc;
    }

    /**
     * Set nbChartProvenWithoutDlc
     * @param integer $nbChartProvenWithoutDlc
     * @return $this
     */
    public function setNbChartProvenWithoutDlc(int $nbChartProvenWithoutDlc): Self
    {
        $this->nbChartProvenWithoutDlc = $nbChartProvenWithoutDlc;
        return $this;
    }

    /**
     * Get nbChartProvenWithoutDlc
     *
     * @return integer
     */
    public function getNbChartProvenWithoutDlc(): int
    {
        return $this->nbChartProvenWithoutDlc;
    }

    /**
     * Set pointGame
     * @param integer $pointGame
     * @return $this
     */
    public function setPointGame(int $pointGame): Self
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
     * Set game
     * @param Game $game
     * @return $this
     */
    public function setGame(Game $game): Self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Set lastUpdate
     * @param DateTime $lastUpdate
     * @return $this
     */
    public function setLastUpdate(DateTime $lastUpdate): Self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return DateTime
     */
    public function getLastUpdate(): DateTime
    {
        return $this->lastUpdate;
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
     * Set player
     * @param Player $player
     * @return $this
     */
    public function setPlayer(Player $player): Self
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Set nbEqual
     * @param integer $nbEqual
     * @return $this
     */
    public function setNbEqual(int $nbEqual): Self
    {
        $this->nbEqual = $nbEqual;
        return $this;
    }

    /**
     * Get nbEqual
     *
     * @return integer
     */
    public function getNbEqual(): int
    {
        return $this->nbEqual;
    }

    /**
     * @param $statuses
     */
    public function setStatuses($statuses)
    {
        $this->statuses = $statuses;
    }

    /**
     * @return mixed
     */
    public function getStatuses()
    {
        return $this->statuses;
    }
}
