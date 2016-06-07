<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_chart", indexes={@ORM\Index(name="idxIdGroup", columns={"idGroup"}), @ORM\Index(name="idxStatus", columns={"status"}), @ORM\Index(name="idxStatusTeam", columns={"statusTeam"}), @ORM\Index(name="idxLibRecordFr", columns={"libRecordFr"}), @ORM\Index(name="idxLibRecordEn", columns={"libRecordEn"}), @ORM\Index(name="idxIdChart", columns={"idChart"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartRepository")
 */
class Chart
{

    /**
     * @var integer
     *
     * @ORM\Column(name="idChart", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="idGroup", type="integer", nullable=false)
     */
    private $idGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="libChartFr", type="string", length=100, nullable=true)
     */
    private $libChartFr;

    /**
     * @var string
     *
     * @ORM\Column(name="libChartEn", type="string", length=100, nullable=false)
     */
    private $libChartEn;

    /**
     * @var string
     *
     * @ORM\Column(name="statusUser", type="string", nullable=false)
     */
    private $statusUser;

    /**
     * @var string
     *
     * @ORM\Column(name="statusTeam", type="string", nullable=false)
     */
    private $statusTeam;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPost", type="integer", nullable=false)
     */
    private $nbPost;

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
     * @var Group
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group", inversedBy="charts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGroup", referencedColumnName="idGroup")
     * })
     */
    private $group;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartLib", mappedBy="chart")
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
     * Get idChart
     *
     * @return integer
     */
    public function getIdChart()
    {
        return $this->idChart;
    }

    /**
     * Get libChart
     *
     * @return string
     */
    public function getLibChart()
    {
        return $this->libChartEn;
    }

    /**
     * Set libChartFr
     *
     * @param string $libChartFr
     * @return Chart
     */
    public function setLibChartFr($libChartFr)
    {
        $this->libChartFr = $libChartFr;
        return $this;
    }

    /**
     * Get libCharFr
     *
     * @return string
     */
    public function getLibChartFr()
    {
        return $this->libChartFr;
    }

    /**
     * Set libChartEn
     *
     * @param string $libChartEn
     * @return Chart
     */
    public function setLibChartEn($libChartEn)
    {
        $this->libChartEn = $libChartEn;
        return $this;
    }

    /**
     * Get libChartEn
     *
     * @return string
     */
    public function getLibChartdEn()
    {
        return $this->libChartEn;
    }

    /**
     * Set statusUser
     *
     * @param string $statusUser
     * @return Chart
     */
    public function setStatusUser($statusUser)
    {
        $this->statusUser = $statusUser;
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatusUser()
    {
        return $this->statusUser;
    }

    /**
     * Set statusTeam
     *
     * @param string $statusTeam
     * @return Chart
     */
    public function setStatusTeam($statusTeam)
    {
        $this->statusTeam = $statusTeam;
        return $this;
    }

    /**
     * Get statusTeam
     *
     * @return string
     */
    public function getStatusTeam()
    {
        return $this->statusTeam;
    }

    /**
     * Set nbPost
     *
     * @param integer $nbPost
     * @return Chart
     */
    public function setNbPost($nbPost)
    {
        $this->nbPost = $nbPost;
        return $this;
    }

    /**
     * Get nbPost
     *
     * @return integer
     */
    public function getNbPost()
    {
        return $this->nbPost;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Chart
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
     * @return Chart
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
     * Set group
     *
     * @param Group $group
     * @return Chart
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * Get group
     *
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param ChartLib $lib
     * @return $this
     */
    public function addLib(ChartLib $lib)
    {
        $this->libs[] = $lib;
        return $this;
    }

    /**
     * @param ChartLib $lib
     */
    public function removeLib(ChartLib $lib)
    {
        $this->libs->removeElement($lib);
    }

    /**
     * @return mixed
     */
    public function getLibs()
    {
        return $this->libs;
    }




}
