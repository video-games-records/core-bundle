<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * UserChart
 *
 * @ORM\Table(name="vgr_user_chart", indexes={@ORM\Index(name="idxIdChart", columns={"idChart"}), @ORM\Index(name="idxIdUser", columns={"idUser"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\UserChartRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserChart
{

    /**
     * This columns are missing on this entity
     *  - preuveImage
     *  - idVideo
     *  - isTopScore
     *  - idPicture
     */


    /**
     * @ORM\Column(name="idUser", type="integer")
     * @ORM\Id
     */
    private $idUser;

    /**
     * @ORM\Column(name="idChart", type="integer")
     * @ORM\Id
     */
    private $idChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer", nullable=false)
     */
    private $rank;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbEqual", type="integer", nullable=false)
     */
    private $nbEqual;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChart", type="float", nullable=false)
     */
    private $pointChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="idEtat", type="integer", nullable=false)
     */
    private $idEtat;

    /**
     * @var integer
     *
     * @ORM\Column(name="isTopScore", type="integer", nullable=false)
     */
    private $isTopScore;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModification", type="datetime", nullable=false)
     */
    private $dateModification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModif", type="datetime", nullable=false)
     */
    private $dateModif;

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
     * Set idUser
     *
     * @param integer $idUser
     * @return UserChart
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
    public function getIdUser()
    {
        return $this->idUser;
    }


    /**
     * Set idChart
     *
     * @param integer $idChart
     * @return UserChart
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
    public function getIdChart()
    {
        return $this->idChart;
    }


    /**
     * Set rank
     *
     * @param integer $rank
     * @return UserChart
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set nbEqual
     *
     * @param integer $nbEqual
     * @return UserChart
     */
    public function setNbEqual($nbEqual)
    {
        $this->nbEqual = $nbEqual;
        return $this;
    }

    /**
     * Get nbEqual
     *
     * @return integer
     */
    public function getNbEqual()
    {
        return $this->nbEqual;
    }

    /**
     * Set pointChart
     *
     * @param float $pointChart
     * @return UserChart
     */
    public function setPointChart($pointChart)
    {
        $this->pointChart = $pointChart;
        return $this;
    }

    /**
     * Get pointChart
     *
     * @return float
     */
    public function getPointChart()
    {
        return $this->pointChart;
    }

    /**
     * Set idEtat
     *
     * @param integer $idEtat
     * @return UserChart
     */
    public function setIdEtat($idEtat)
    {
        $this->idEtat = $idEtat;
        return $this;
    }

    /**
     * Get idEtat
     *
     * @return integer
     */
    public function getIdEtat()
    {
        return $this->idEtat;
    }

    /**
     * Set isTopScore
     *
     * @param integer $isTopScore
     * @return UserChart
     */
    public function setIsTopScore($isTopScore)
    {
        $this->isTopScore = $isTopScore;
        return $this;
    }

    /**
     * Get isTopScore
     *
     * @return integer
     */
    public function getIsTopScore()
    {
        return $this->isTopScore;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return UserChart
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
     * Set dateModification
     *
     * @param \DateTime $dateModification
     * @return UserChart
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;
        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set dateModif
     *
     * @param \DateTime $dateModif
     * @return UserChart
     */
    public function setDateModif($dateModif)
    {
        $this->dateModif = $dateModif;
        return $this;
    }

    /**
     * Get dateModif
     *
     * @return \DateTime
     */
    public function getDateModif()
    {
        return $this->dateModif;
    }


    /**
     * Set chart
     *
     * @param Chart $chart
     * @return UserChart
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
     * @return UserChart
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
        $this->setNbEqual(0);
        $this->setPointRecord(0);
        $this->setRank(10000);
        $this->setDateCreation(new \DateTime());
        $this->setDateModification(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     * @param $event
     */
    public function preUpdate($event)
    {
        if ($this->getRank() == 1) {
            $this->setIsTopScore(1);
        } else {
            $this->setIsTopScore(0);
        }
    }

}