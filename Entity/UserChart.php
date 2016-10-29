<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * UserChart
 *
 * @ORM\Table(name="vgr_user_chart", indexes={@ORM\Index(name="idxIdChart", columns={"idChart"}), @ORM\Index(name="idxIdUser", columns={"idUser"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\UserChartRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserChart
{
    /**
     * This columns are missing on this entity
     *  - preuveImage
     *  - idVideo
     *  - idPicture
     */
    use Timestampable;

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
    private $nbEqual = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChart", type="float", nullable=false)
     */
    private $pointChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="idEtat", type="integer", nullable=false)
     */
    private $idEtat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isTopScore", type="boolean", nullable=false)
     */
    private $topScore = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModif", type="datetime", nullable=false)
     */
    private $dateModif;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
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
     * Set topScore
     *
     * @param integer $topScore
     * @return UserChart
     */
    public function setTopScore($topScore)
    {
        $this->topScore = $topScore;
        return $this;
    }

    /**
     * Get topScore
     *
     * @return integer
     */
    public function getTopScore()
    {
        return $this->topScore;
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
     * @param Player $user
     * @return UserChart
     */
    public function setUser(Player $user = null)
    {
        $this->user = $user;
        $this->setIdUser($user->getIdUser());
        return $this;
    }

    /**
     * Get user
     *
     * @return Player
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @ORM\PrePersist()
     */
    public function preInsert()
    {
        $this->setPointRecord(0);
        $this->setRank(10000);
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        if ($this->getRank() == 1) {
            $this->setTopScore(true);
        } else {
            $this->setTopScore(false);
        }
    }
}
