<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Model\Entity\NbChartProvenTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\NbChartProvenWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\NbChartWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\NbEqualTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankPointChartTrait;

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
    use NbChartWithoutDlcTrait;
    use NbChartProvenTrait;
    use NbChartProvenWithoutDlcTrait;
    use NbEqualTrait;
    use RankMedalTrait;
    use RankPointChartTrait;

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
     * @var integer
     *
     * @ORM\Column(name="pointChartWithoutDlc", type="integer", nullable=false)
     */
    private int $pointChartWithoutDlc = 0;

    /**
     * @ORM\Column(name="pointGame", type="integer", nullable=false)
     */
    private int $pointGame = 0;

    /**
     * @ORM\Column(name="lastUpdate", type="datetime", nullable=false)
     */
    private DateTime $lastUpdate;


    private $statuses;


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
