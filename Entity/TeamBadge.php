<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use ProjetNormandie\BadgeBundle\Entity\Badge;

/**
 * PlayerGame
 *
 * @ORM\Table(name="vgr_team_badge", indexes={@ORM\Index(name="idxIdBadge", columns={"idBadge"}), @ORM\Index(name="idxIdTeam", columns={"idTeam"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository")
 */
class TeamBadge
{
    use Timestampable;

    /**
     * @ORM\Column(name="idTeam", type="integer")
     * @ORM\Id
     */
    private $idTeam;

    /**
     * @ORM\Column(name="idBadge", type="integer")
     * @ORM\Id
     */
    private $idBadge;

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
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="idTeam")
     * })
     */
    private $team;

    /**
     * @var Badge
     *
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\BadgeBundle\Entity\Badge", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="idBadge")
     * })
     */
    private $badge;

    /**
     * Set idTeam
     *
     * @param integer $idTeam
     * @return $this
     */
    public function setIdTeam($idTeam)
    {
        $this->idTeam = $idTeam;
        return $this;
    }

    /**
     * Get idTeam
     *
     * @return integer
     */
    public function getIdTeam()
    {
        return $this->idTeam;
    }


    /**
     * Set idBadge
     *
     * @param integer $idBadge
     * @return $this
     */
    public function setIdBadge($idBadge)
    {
        $this->idBadge = $idBadge;
        return $this;
    }

    /**
     * Get idBadge
     *
     * @return integer
     */
    public function getIdBadge()
    {
        return $this->idBadge;
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
     * @param Badge $badge
     * @return $this
     */
    public function setBadge(Badge $badge = null)
    {
        $this->badge = $badge;
        $this->setIdBadge($badge->getIdBadge());
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
        $this->setIdTeam($team->getIdTeam());
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
