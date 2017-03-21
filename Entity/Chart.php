<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_chart", indexes={@ORM\Index(name="idxIdGroup", columns={"idGroup"}), @ORM\Index(name="idxStatusPlayer", columns={"statusPlayer"}), @ORM\Index(name="idxStatusTeam", columns={"statusTeam"}), @ORM\Index(name="idxStatusTeam", columns={"statusTeam"}), @ORM\Index(name="idxLibChartFr", columns={"libChartFr"}), @ORM\Index(name="idxLibChartEn", columns={"libChartEn"}), @ORM\Index(name="idxIdChart", columns={"idChart"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartRepository")
 */
class Chart
{
    use Timestampable;

    const STATUS_NORMAL = 'NORMAL';
    const STATUS_MAJ = 'MAJ';
    const STATUS_GO_TO_MAJ = 'goToMAJ';
    const STATUS_ERROR = 'ERREUR';
    const STATUS_WORK_DELETE = 'WORK_DELETE';

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
     * @Assert\Length(max="100")
     * @ORM\Column(name="libChartFr", type="string", length=100, nullable=true)
     */
    private $libChartFr;

    /**
     * @var string
     *
     * @Assert\Length(max="100")
     * @ORM\Column(name="libChartEn", type="string", length=100, nullable=false)
     */
    private $libChartEn;

    /**
     * @var string
     *
     * @ORM\Column(name="statusPlayer", type="string", nullable=false)
     */
    private $statusPlayer = 'NORMAL';

    /**
     * @var string
     *
     * @ORM\Column(name="statusTeam", type="string", nullable=false)
     */
    private $statusTeam = 'NORMAL';

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPost", type="integer", nullable=false)
     */
    private $nbPost = 0;

    /**
     * @var Group
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group", inversedBy="charts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGroup", referencedColumnName="id")
     * })
     */
    private $group;

    /**
     * @var ArrayCollection|\VideoGamesRecords\CoreBundle\Entity\ChartLib[]
     *
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartLib", mappedBy="chart", cascade={"persist", "remove"}, orphanRemoval=true)
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
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->libChartEn, $this->idChart);
    }

    /**
     * Set idChart
     *
     * @param integer $idChart
     * @return Chart
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
     * Set idGroup
     *
     * @param integer $idGroup
     * @return Chart
     */
    public function setIdGroup($idGroup)
    {
        $this->idGroup = $idGroup;

        return $this;
    }

    /**
     * Get idGroup
     *
     * @return integer
     */
    public function getIdGroup()
    {
        return $this->idGroup;
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
    public function getLibChartEn()
    {
        return $this->libChartEn;
    }


    /**
     * Set statusPlayer
     *
     * @param string $statusPlayer
     * @return Chart
     */
    public function setStatusPlayer($statusPlayer)
    {
        $this->statusPlayer = $statusPlayer;
        return $this;
    }

    /**
     * Get statusPlayer
     *
     * @return string
     */
    public function getStatusPlayer()
    {
        return $this->statusPlayer;
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
     * Set group
     *
     * @param Group $group
     * @return Chart
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;
        $this->setIdGroup($group->getId());
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
        $lib->setChart($this);
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
     * @return ArrayCollection|\VideoGamesRecords\CoreBundle\Entity\ChartLib[]
     */
    public function getLibs()
    {
        return $this->libs;
    }


    /**
     * @return array
     */
    public static function getStatusChoices()
    {
        return [
            self::STATUS_NORMAL => self::STATUS_NORMAL,
            self::STATUS_MAJ => self::STATUS_MAJ,
            self::STATUS_GO_TO_MAJ => self::STATUS_GO_TO_MAJ,
            self::STATUS_ERROR => self::STATUS_ERROR,
            self::STATUS_WORK_DELETE => self::STATUS_WORK_DELETE,
        ];
    }
}
