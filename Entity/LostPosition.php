<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LostPosition
 *
 * @ORM\Table(name="vgr_lostposition", indexes={@ORM\Index(name="idxIdUser", columns={"idUser"}), @ORM\Index(name="idxIdChart", columns={"idChart"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\LostPositionRepository")
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\Column(name="idUser", type="integer", nullable=false, options={"default":0})
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="idChart", type="integer", nullable=false, options={"default":0})
     */
    private $idChart;

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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="idUser")
     * })
     */
    private $user;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="idChart")
     * })
     */
    private $chart;

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
     * Set idUser
     *
     * @param integer $idUser
     * @return LostPosition
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function geIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idChart
     *
     * @param integer $idChart
     * @return LostPosition
     */
    public function setIdChart($idChart)
    {
        $this->idChart = $idChart;
        return $this;
    }

    /**
     * Get idChart
     *
     * @return integer
     */
    public function geIdChart()
    {
        return $this->idChart;
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
        $this->setIdChart($chart->getIdChart());
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
     * Set user
     *
     * @param User $user
     * @return LostPosition
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        $this->setIdUser($user->getIdUser());
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @ORM\PrePersist
     */
    public function preInsert()
    {
        $this->setDateCreation(new \DateTime());
    }

}
