<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="vgr_team_demand")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamDemandRepository")
 */
class TeamDemand
{
    use Timestampable;

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_REFUSED = 'REFUSED';

    /**
     * @var integer
     *
     * @ORM\Column(name="idDemand", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idDemand;

    /**
     * @var integer
     *
     * @ORM\Column(name="idTeam", type="integer")
     */
    private $idTeam;

    /**
     * @var integer
     *
     * @ORM\Column(name="idPlayer", type="integer")
     */
    private $idPlayer;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_ACTIVE;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team", inversedBy="teamDemand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="idTeam")
     * })
     */
    private $team;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="teamDemand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="idPlayer")
     * })
     */
    private $player;


    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set idDemand
     *
     * @param integer $idDemand
     * @return $this
     */
    public function setIdDemand($idDemand)
    {
        $this->idDemand = $idDemand;
        return $this;
    }

    /**
     * Get idDemand
     *
     * @return integer
     */
    public function getIdDemand()
    {
        return $this->idDemand;
    }

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
     * Set idPlayer
     *
     * @param integer $idPlayer
     * @return $this
     */
    public function setIdPlayer($idPlayer)
    {
        $this->idPlayer = $idPlayer;
        return $this;
    }

    /**
     * Get idPlayer
     *
     * @return integer
     */
    public function getIdPlayer()
    {
        return $this->idPlayer;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set player
     *
     * @param Player $player
     * @return $this
     */
    public function setPlayer(Player $player = null)
    {
        $this->player = $player;
        $this->setIdPlayer($player->getIdPlayer());
        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set team
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
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }
}
