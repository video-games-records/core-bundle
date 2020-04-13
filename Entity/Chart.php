<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_chart", indexes={@ORM\Index(name="idxIdGroup", columns={"idGroup"}), @ORM\Index(name="idxStatusPlayer", columns={"statusPlayer"}), @ORM\Index(name="idxStatusTeam", columns={"statusTeam"}), @ORM\Index(name="idxStatusTeam", columns={"statusTeam"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartRepository")
 * @method ChartTranslation translate(string $locale, bool $fallbackToDefault)
 */
class Chart implements SluggableInterface, TimestampableInterface, TranslatableInterface
{
    use TimestampableTrait;
    use TranslatableTrait;
    use SluggableTrait;

    const STATUS_NORMAL = 'NORMAL';
    const STATUS_MAJ = 'MAJ';
    const STATUS_GO_TO_MAJ = 'goToMAJ';
    const STATUS_ERROR = 'ERREUR';
    const STATUS_WORK_DELETE = 'WORK_DELETE';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     *   @ORM\JoinColumn(name="idGroup", referencedColumnName="id", nullable=false)
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
     * @var ArrayCollection|\VideoGamesRecords\CoreBundle\Entity\PlayerChart[]
     *
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChart", mappedBy="chart")
     */
    private $playerCharts;

    /**
     * Shortcut to playerChart.rank = 1
     * @var PlayerChart
     */
    private $playerChart1;

    /**
     * Shortcut to playerChart.player = player
     * @var PlayerChart
     */
    private $playerChartP;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->libs = new ArrayCollection();
        $this->playerCharts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    /**
     * @return string
     */
    public function getDefaultName()
    {
        return $this->translate('en', false)->getName();
    }

    /**
     * Set idChart
     *
     * @param integer $id
     * @return Chart
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get idChart
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->translate(null, false)->setName($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->translate(null, false)->getName();
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
     * @return \Doctrine\Common\Collections\ArrayCollection|\VideoGamesRecords\CoreBundle\Entity\PlayerChart[]
     */
    public function getPlayerCharts()
    {
        return $this->playerCharts;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|\VideoGamesRecords\CoreBundle\Entity\PlayerChart[] $playerCharts
     *
     * @return Chart
     */
    public function setPlayerCharts($playerCharts)
    {
        $this->playerCharts = $playerCharts;

        return $this;
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
     * @param $playerChart1
     */
    public function setPlayerChart1($playerChart1)
    {
        $this->playerChart1 = $playerChart1;
    }

    /**
     * @return mixed
     */
    public function getPlayerChart1()
    {
        return $this->playerChart1;
    }

    /**
     * @param $playerChartP
     */
    public function setPlayerChartP($playerChartP)
    {
        $this->playerChartP = $playerChartP;
    }

    /**
     * @return mixed
     */
    public function getPlayerChartP()
    {
        return $this->playerChartP;
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

    /**
     * Returns an array of the fields used to generate the slug.
     *
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['defaultName'];
    }
}
