<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;

/**
 * PlayerGame
 *
 * @ORM\Table(name="vgr_player_badge")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\PlayerBadgeListener"})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "player": "exact",
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
 *          "whitelist": {
 *              "playerBadge.read",
 *              "playerBadge.badge",
 *              "playerBadge.player",
 *              "player.read.mini",
 *              "badge.read",
 *              "badge.game",
 *              "badge.serie",
 *              "serie.read"
 *          }
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
class PlayerBadge implements BadgeInterface
{
    use TimestampableEntity;

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
    private ?int $mbOrder = null;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Player $player;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Badge", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Badge $badge;


    /**
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): static
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
    public function setEndedAt(DateTime $ended_at): static
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
    public function setMbOrder(int $mbOrder): static
    {
        $this->mbOrder = $mbOrder;

        return $this;
    }

    /**
     * Get mbOrder
     *
     * @return integer
     */
    public function getMbOrder(): ?int
    {
        return $this->mbOrder;
    }

    /**
     * Set badge
     *
     * @param Badge $badge
     * @return $this
     */
    public function setBadge(Badge $badge): static
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
    public function setPlayer(Player $player): static
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


    public function __toString(): string
    {
        return sprintf('%s # %s ', $this->getPlayer()->getPseudo(), $this->getBadge()->__toString());
    }
}
