<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use ProjetNormandie\BadgeBundle\Entity\Badge;

/**
 * PlayerGame
 *
 * @ORM\Table(name="vgr_player_badge", indexes={@ORM\Index(name="idxIdBadge", columns={"idBadge"}), @ORM\Index(name="idxIdPlayer", columns={"idPlayer"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository")
 */
class PlayerBadge
{
    use Timestampable;

    /**
     * @ORM\Column(name="idPlayer", type="integer")
     * @ORM\Id
     */
    private $idPlayer;

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
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="playerBadge")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="idPlayer")
     * })
     */
    private $player;

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
}
