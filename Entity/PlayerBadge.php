<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * PlayerGame
 *
 * @ORM\Table(name="vgr_player_badge")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository")
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "player": "exact",
 *          "badge": "exact",
 *          "badge.type": "exact",
 *      }
 *)
 * @ApiFilter(DateFilter::class, properties={"ended_at": DateFilter::INCLUDE_NULL_BEFORE_AND_AFTER})
 * @ApiResource(attributes={"order"={"badge.type", "badge.value"}})
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *          "parameterName": "groups",
 *          "overrideDefaultGroups": true,
 *          "whitelist": {"playerBadge.read","playerBadge.badge","playerBadge.player","player.read.mini", "badge.read", "badge.game"}
 *     }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id":"ASC",
 *          "createdAt":"ASC",
 *          "mbOrder":"ASC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class PlayerBadge implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="ended_at", type="datetime", nullable=true)
     */
    private ?DateTime $ended_at = null;

    /**
     * @ORM\Column(name="mbOrder", type="integer", nullable=true, options={"default":0})
     */
    private int $mbOrder = 0;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="playerBadge")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private Player $player;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Badge", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="id", nullable=false)
     * })
     */
    private Badge $badge;

    /**
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): Self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set ended_at
     *
     * @param DateTime $ended_at
     * @return $this
     */
    public function setEndedAt(DateTime $ended_at): Self
    {
        $this->ended_at = $ended_at;

        return $this;
    }

    /**
     * Get ended_at
     *
     * @return DateTime
     */
    public function getEndedAt(): ?DateTime
    {
        return $this->ended_at;
    }

    /**
     * Set mbOrder
     *
     * @param integer $mbOrder
     * @return $this
     */
    public function setMbOrder(int $mbOrder): Self
    {
        $this->mbOrder = $mbOrder;

        return $this;
    }

    /**
     * Get mbOrder
     *
     * @return integer
     */
    public function getMbOrder(): int
    {
        return $this->mbOrder;
    }

    /**
     * Set badge
     *
     * @param Badge $badge
     * @return $this
     */
    public function setBadge(Badge $badge): Self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get badge
     *
     * @return Badge
     */
    public function getBadge(): Badge
    {
        return $this->badge;
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
}
