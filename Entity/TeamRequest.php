<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Entity\Player;

/**
 * TeamRequest
 *
 * @ORM\Table(name="vgr_team_request")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamRequestRepository")
 */
class TeamRequest
{
    use Timestampable;

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_REFUSED = 'REFUSED';

    /**
     * @var integer
     *
     * @ORM\Column(name="idRequest", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRequest;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_ACTIVE;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="idTeam", nullable=false)
     * })
     */
    private $team;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private $player;

    /**
     * Set idRequest
     *
     * @param integer $idRequest
     * @return $this
     */
    public function setIdRequest($idRequest)
    {
        $this->idRequest = $idRequest;
        return $this;
    }

    /**
     * Get idRequest
     *
     * @return integer
     */
    public function getIdRequest()
    {
        return $this->idRequest;
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
