<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserChart
 *
 * @ORM\Table(name="vgr_record_membre", indexes={@ORM\Index(name="idxIdRecord", columns={"idRecord"}), @ORM\Index(name="idxIdMembre", columns={"idMembre"})})
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
     * @ORM\Column(name="idMembre", type="integer")
     * @ORM\Id
     */
    private $idMembre;

    /**
     * @ORM\Column(name="idRecord", type="integer")
     * @ORM\Id
     */
    private $idRecord;

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
     * @ORM\Column(name="pointRecord", type="float", nullable=false)
     */
    private $pointRecord;

    /**
     * @var integer
     *
     * @ORM\Column(name="idEtat", type="integer", nullable=false)
     */
    private $idEtat;

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
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMembre", referencedColumnName="idMembre")
     * })
     */
    private $user;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idRecord", referencedColumnName="idRecord")
     * })
     */
    private $chart;


    /**
     * Set idMembre
     *
     * @param integer $idMembre
     * @return UserChart
     */
    public function setIdMembre($idMembre)
    {
        $this->idMembre = $idMembre;
        return $this;
    }

    /**
     * Get idMembre
     *
     * @return integer
     */
    public function geIdMembre()
    {
        return $this->idMembre;
    }


    /**
     * Set idRecord
     *
     * @param integer $idRecord
     * @return UserChart
     */
    public function setIdRecord($idRecord)
    {
        $this->idRecord = $idRecord;
        return $this;
    }

    /**
     * Get idRecord
     *
     * @return integer
     */
    public function geIdRecord()
    {
        return $this->idRecord;
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
     * Set pointRecord
     *
     * @param float $pointRecord
     * @return UserChart
     */
    public function setPointRecord($pointRecord)
    {
        $this->pointRecord = $pointRecord;
        return $this;
    }

    /**
     * Get pointRecord
     *
     * @return float
     */
    public function getPointRecord()
    {
        return $this->pointRecord;
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
        $this->setIdRecord($chart->getIdRecord());
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
        $this->setIdMembre($user->getIdMembre());
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
        $this->setDateCreation(new \DateTime());
        $this->setDateModification(new \DateTime());
    }


}