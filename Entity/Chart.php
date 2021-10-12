<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Intl\Locale;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_chart")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\ChartListener"})
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id":"ASC",
 *          "libChartEn" : "ASC",
 *          "libChartFr" : "ASC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class Chart implements SluggableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    const STATUS_NORMAL = 'NORMAL';
    const STATUS_MAJ = 'MAJ';
    const STATUS_GO_TO_MAJ = 'goToMAJ';
    const STATUS_ERROR = 'ERROR';

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
     * @Assert\Length(max="255")
     * @ORM\Column(name="libChartEn", type="string", length=255, nullable=false)
     */
    private $libChartEn;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     * @ORM\Column(name="libChartFr", type="string", length=255, nullable=false)
     */
    private $libChartFr;

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
     * @var ArrayCollection|ChartLib[]
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartLib", mappedBy="chart", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $libs;

    /**
     * @var ArrayCollection|PlayerChart[]
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChart", mappedBy="chart")
     */
    private $playerCharts;

    /**
     * @var ArrayCollection|LostPosition[]
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\LostPosition", mappedBy="chart")
     */
    private $lostPositions;

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
        return $this->libChartEn;
    }

     /**
     * @return string
     */
    public function getName(): string
    {
        $locale = Locale::getDefault();
        if ($locale == 'fr') {
            return $this->libChartFr;
        } else {
            return $this->libChartEn;
        }
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getCompleteName($locale = 'en'): string
    {
        if ($locale == 'fr') {
            return $this->getGroup()->getGame()->getLibGameFr() . ' - ' .
            $this->getGroup()->getLibGroupFr()  . ' - ' .
            $this->getLibChartFr();
        } else {
            return $this->getGroup()->getGame()->getLibGameEn() . ' - ' .
            $this->getGroup()->getLibGroupEn()  . ' - ' .
            $this->getLibChartEn();
        }
    }

    /**
     * Set idChart
     *
     * @param integer $id
     * @return Chart
     */
    public function setId(int $id)
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
     * @param string $libChartEn
     * @return $this
     */
    public function setLibChartEn(string $libChartEn): Chart
    {
        $this->libChartEn = $libChartEn;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibChartEn(): ?string
    {
        return $this->libChartEn;
    }

    /**
     * @param string $libChartFr
     * @return $this
     */
    public function setLibChartFr(string $libChartFr): Chart
    {
        $this->libChartFr = $libChartFr;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibChartFr(): ?string
    {
        return $this->libChartFr;
    }

    /**
     * Set statusPlayer
     *
     * @param string $statusPlayer
     * @return Chart
     */
    public function setStatusPlayer(string $statusPlayer)
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
    public function setStatusTeam(string $statusTeam)
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
     * @return ArrayCollection|PlayerChart[]
     */
    public function getPlayerCharts()
    {
        return $this->playerCharts;
    }

    /**
     * @return ArrayCollection|LostPosition[]
     */
    public function getLostPositions()
    {
        return $this->lostPositions;
    }

    /**
     * @param ArrayCollection|PlayerChart[] $playerCharts
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
    public function setNbPost(int $nbPost)
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
     * @param Group|null $group
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
     * @return ArrayCollection|ChartLib[]
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
     * @return string
     */
    public function getUrl(): string
    {
        return sprintf(
            '%s-game-g%d/%s-group-g%d/%s-chart-c%d/index',
            $this->getGroup()->getGame()->getSlug(),
            $this->getGroup()->getGame()->getId(),
            $this->getGroup()->getSlug(),
            $this->getGroup()->getId(),
            $this->getSlug(),
            $this->getId()
        );
    }

    /**
     * @return array
     */
    public static function getStatusChoices(): array
    {
        return [
            'label.chart.status.normal' => self::STATUS_NORMAL,
            'label.chart.status.maj' => self::STATUS_MAJ,
            'label.chart.status.goToMaj' => self::STATUS_GO_TO_MAJ,
            'label.chart.status.error' => self::STATUS_ERROR,
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
