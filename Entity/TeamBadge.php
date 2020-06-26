<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * TeamBadge
 *
 * @ORM\Table(name="vgr_team_badge", indexes={@ORM\Index(name="idxIdBadge", columns={"idBadge"}), @ORM\Index(name="idxIdTeam", columns={"idTeam"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository")
 * @ApiFilter(SearchFilter::class, properties={"team": "exact"})
 * @ApiFilter(DateFilter::class, properties={"ended_at": DateFilter::INCLUDE_NULL_BEFORE_AND_AFTER})
 * @ApiResource(attributes={"order"={"badge.type", "badge.value"}})
 */
class TeamBadge implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ended_at", type="datetime", nullable=true)
     */
    private $ended_at;

    /**
     * @var integer
     *
     * @ORM\Column(name="mbOrder", type="integer", nullable=true, options={"default":0})
     */
    private $mbOrder = 0;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team", inversedBy="teamBadge")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="id", nullable=false)
     * })
     */
    private $team;

    /**
     * @var Badge
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\BadgeInterface", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="id", nullable=false)
     * })
     */
    private $badge;

    /**
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ended_at
     *
     * @param \DateTime $ended_at
     * @return $this
     */
    public function setEndedAt($ended_at)
    {
        $this->ended_at = $ended_at;

        return $this;
    }

    /**
     * Get ended_at
     *
     * @return \DateTime
     */
    public function getEndedAt()
    {
        return $this->ended_at;
    }

    /**
     * Set mbOrder
     *
     * @param integer $mbOrder
     * @return $this
     */
    public function setMbOrder($mbOrder)
    {
        $this->mbOrder = $mbOrder;

        return $this;
    }

    /**
     * Get mbOrder
     *
     * @return integer
     */
    public function getMbOrder()
    {
        return $this->mbOrder;
    }


    /**
     * Set badge
     *
     * @param $badge
     * @return $this
     */
    public function setBadge($badge = null)
    {
        $this->badge = $badge;
        return $this;
    }

    /**
     * Get badge
     *
     * @return Badge
     */
    public function getBadge()
    {
        return $this->badge;
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
