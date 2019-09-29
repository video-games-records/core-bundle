<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\ProofBundle\Entity\Proof;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * PlayerChart
 *
 * @ORM\Table(name="vgr_player_chart", indexes={@ORM\Index(name="idxIdChart", columns={"idChart"}), @ORM\Index(name="idxIdPlayer", columns={"idPlayer"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PlayerChart
{
    use Timestampable;
    use \VideoGamesRecords\CoreBundle\Model\Player;

    /**
     * @var integer
     *
     * @ORM\Column(name="idPlayerChart", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPlayerChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer", nullable=true)
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
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private $pointChart = 0;

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
     * @var \DateTime
     *
     * @ORM\Column(name="dateInvestigation", type="date", nullable=true)
     */
    private $dateInvestigation;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", inversedBy="playerCharts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id", nullable=false)
     * })
     */
    private $chart;

    /**
     * @var Proof
     *
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\ProofBundle\Entity\Proof")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idProof", referencedColumnName="idProof")
     * })
     */
    private $proof;

    /**
     * @var PlayerChartStatus
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStatus", referencedColumnName="idStatus", nullable=false)
     * })
     */
    private $status;

    /**
     * @var Platform
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Platform")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlatform", referencedColumnName="id")
     * })
     */
    private $platform;

    /**
     * @var ArrayCollection|\VideoGamesRecords\CoreBundle\Entity\PlayerChartLib[]
     *
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChartLib", mappedBy="playerChart", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $libs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->libs = new ArrayCollection();
    }

    /**
     * Set idPlayerChart
     *
     * @param integer $idPlayerChart
     * @return PlayerChart
     */
    public function setIdPlayerChart($idPlayerChart)
    {
        $this->idPlayerChart = $idPlayerChart;
        return $this;
    }

    /**
     * Get idPlayerChart
     *
     * @return integer
     */
    public function getIdPlayerChart()
    {
        return $this->idPlayerChart;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return PlayerChart
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
     * @return PlayerChart
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
     * @return PlayerChart
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
     * Set topScore
     *
     * @param bool $topScore
     *
     * @return PlayerChart
     */
    public function setTopScore($topScore)
    {
        $this->topScore = $topScore;
        return $this;
    }

    /**
     * Get topScore
     *
     * @return bool
     */
    public function isTopScore()
    {
        return $this->topScore;
    }

    /**
     * Set dateModif
     *
     * @param \DateTime $dateModif
     * @return PlayerChart
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
     * Set dateInvestigation
     *
     * @param \DateTime $dateInvestigation
     * @return PlayerChart
     */
    public function setDateInvestigation($dateInvestigation)
    {
        $this->dateInvestigation = $dateInvestigation;
        return $this;
    }

    /**
     * Get dateInvestigation
     *
     * @return \DateTime
     */
    public function getDateInvestigation()
    {
        return $this->dateInvestigation;
    }

    /**
     * Set chart
     *
     * @param Chart $chart
     * @return PlayerChart
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
     * Set proof
     *
     * @param Proof $proof
     * @return PlayerChart
     */
    public function setProof(Proof $proof = null)
    {
        $this->proof = $proof;

        return $this;
    }

    /**
     * Get proof
     *
     * @return Proof
     */
    public function getProof()
    {
        return $this->proof;
    }


    /**
     * Set platform
     *
     * @param Platform $platform
     * @return PlayerChart
     */
    public function setPlatform(Platform $platform = null)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * Get platform
     *
     * @return Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }


    /**
     * Set status
     *
     * @param PlayerChartStatus $status
     * @return PlayerChart
     */
    public function setStatus(PlayerChartStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return PlayerChartStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param PlayerChartLib $lib
     * @return $this
     */
    public function addLib(PlayerChartLib $lib)
    {
        $lib->setPlayerChart($this);
        $this->libs[] = $lib;
        return $this;
    }

    /**
     * @param PlayerChartLib $lib
     */
    public function removeLib(PlayerChartLib $lib)
    {
        $this->libs->removeElement($lib);
    }

    /**
     * @return ArrayCollection|\VideoGamesRecords\CoreBundle\Entity\PlayerChartLib[]
     */
    public function getLibs()
    {
        return $this->libs;
    }


    /**
     * @ORM\PrePersist()
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entityManager = $args->getObjectManager();
        $this->setStatus($entityManager->getReference('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus', 1));
        $this->setDateModif(new \DateTime());
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->setTopScore(false);
        if ($this->getRank() === 1) {
            $this->setTopScore(true);
        }

        if (null === $this->getDateInvestigation() && PlayerChartStatus::ID_STATUS_INVESTIGATION === $this->getStatus()->getIdStatus()) {
            $this->setDateInvestigation(new \DateTime());
        }
        if (null !== $this->getDateInvestigation() && in_array($this->getStatus()->getIdStatus(), [PlayerChartStatus::ID_STATUS_PROOVED, PlayerChartStatus::ID_STATUS_NOT_PROOVED], true)) {
            $this->setDateInvestigation(null);
        }

        /*$playerChart->setStatus($em->getReference('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus', 1));
        $playerChart->setDateModif(new \DateTime());*/
    }
}
