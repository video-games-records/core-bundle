<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LostPosition
 *
 * @ORM\Table(name="vgr_lostposition", indexes={@ORM\Index(name="idxIdPlayer", columns={"idPlayer"}), @ORM\Index(name="idxIdChart", columns={"idChart"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\LostPositionRepository")
 */
class LostPosition
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="oldRank", type="integer", nullable=false, options={"default":0})
     */
    private $oldRank;

    /**
     * @var integer
     *
     * @ORM\Column(name="newRank", type="integer", nullable=false, options={"default":0})
     */
    private $newRank;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="idPlayer", nullable=false)
     * })
     */
    private $player;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id", nullable=false)
     * })
     */
    private $chart;

    public function __construct()
    {
        $this->setDateCreation(new \DateTime());
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return LostPosition
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
     * Set newRank
     *
     * @param integer $newRank
     * @return LostPosition
     */
    public function setNewRank($newRank)
    {
        $this->newRank = $newRank;
        return $this;
    }

    /**
     * Get newRank
     *
     * @return integer
     */
    public function getNewRank()
    {
        return $this->newRank;
    }

    /**
     * Set oldRank
     *
     * @param integer $oldRank
     * @return LostPosition
     */
    public function setOldRank($oldRank)
    {
        $this->oldRank = $oldRank;
        return $this;
    }

    /**
     * Get oldRank
     *
     * @return integer
     */
    public function getOldRank()
    {
        return $this->oldRank;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return LostPosition
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set chart
     *
     * @param Chart $chart
     * @return LostPosition
     */
    public function setChart(Chart $chart = null)
    {
        $this->chart = $chart;

        return $this;
    }

    /**
     * Get chart
     *
     * @return Chart
     */
    public function getChart()
    {
        return $this->chart;
    }


    /**
     * Set player
     *
     * @param Player $player
     * @return LostPosition
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
}
