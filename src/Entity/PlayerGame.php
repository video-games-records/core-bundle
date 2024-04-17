<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Traits\Entity\Game\GameMethodsTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbEqualTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerMethodsTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;

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
 *              "playerGame.lastUpdate",
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
 *          "lastUpdate": "DESC",
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
    use PlayerMethodsTrait;
    use GameMethodsTrait;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="playerGame")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Player $player;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="playerGame", fetch="EAGER")
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
    public function setPointChartWithoutDlc(int $pointChartWithoutDlc): static
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
    public function setPointGame(int $pointGame): static
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
     * Set lastUpdate
     * @param DateTime $lastUpdate
     * @return $this
     */
    public function setLastUpdate(DateTime $lastUpdate): static
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
