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
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * TeamBadge
 *
 * @ORM\Table(name="vgr_team_badge")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository")
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "team": "exact",
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
 *          "whitelist": {"teamBadge.read","teamBadge.badge","teamBadge.team","team.read.mini"}
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
class TeamBadge implements TimestampableInterface
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
    private ?int $mbOrder = null;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team", inversedBy="teamBadge")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Team $team;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Badge", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Badge $badge;

    /**
     * Set id
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): self
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
     * @param DateTime|null $ended_at
     * @return $this
     */
    public function setEndedAt(DateTime $ended_at = null): self
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
     * @param integer $mbOrder
     * @return $this
     */
    public function setMbOrder(int $mbOrder): self
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
     * @param $badge
     * @return $this
     */
    public function setBadge($badge = null): self
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
     * Set team
     * @param Team $team
     * @return $this
     */
    public function setTeam(Team $team): self
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
