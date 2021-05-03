<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Intl\Locale;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Group
 *
 * @ORM\Table(name="vgr_group")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GroupRepository")
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id":"ASC",
 *          "libGroupEn" : "ASC",
 *          "libGroupFr" : "ASC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class Group implements SluggableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     * @ORM\Column(name="libGroupEn", type="string", length=255, nullable=false)
     */
    private $libGroupEn;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     * @ORM\Column(name="libGroupFr", type="string", length=255, nullable=false)
     */
    private $libGroupFr;

    /**
     * @var boolean
     * @ORM\Column(name="boolDlc", type="boolean", nullable=false)
     */
    private $boolDlc = false;

    /**
     * @var integer
     * @ORM\Column(name="nbChart", type="integer", nullable=false)
     */
    private $nbChart = 0;

    /**
     * @var integer
     * @ORM\Column(name="nbPost", type="integer", nullable=false)
     */
    private $nbPost = 0;

    /**
     * @var integer
     * @ORM\Column(name="nbPlayer", type="integer", nullable=false)
     */
    private $nbPlayer = 0;

    /**
     * @var Game
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="groups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false)
     * })
     */
    private $game;

    /**
     * @var Chart[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", mappedBy="group",cascade={"persist"})
     */
    private $charts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->charts = new ArrayCollection();
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
    public function getDefaultName(): string
    {
        return $this->libGroupEn;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $locale = Locale::getDefault();
        if ($locale == 'fr') {
            return $this->libGroupFr;
        } else {
            return $this->libGroupEn;
        }
    }

    /**
     * Set idGroup
     * @param integer $id
     * @return Group
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get idGroup
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $libGroupEn
     * @return $this
     */
    public function setLibGroupEn(string $libGroupEn): Group
    {
        $this->libGroupEn = $libGroupEn;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibGroupEn(): ?string
    {
        return $this->libGroupEn;
    }

    /**
     * @param string $libGroupFr
     * @return $this
     */
    public function setLibGroupFr(string $libGroupFr): Group
    {
        $this->libGroupFr = $libGroupFr;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibGroupFr(): ?string
    {
        return $this->libGroupFr;
    }

    /**
     * Set boolDlc
     * @param boolean $boolDlc
     * @return Group
     */
    public function setBoolDlc(bool $boolDlc)
    {
        $this->boolDlc = $boolDlc;

        return $this;
    }

    /**
     * Get boolDlc
     * @return boolean
     */
    public function getBoolDlc()
    {
        return $this->boolDlc;
    }

    /**
     * Set nbChart
     * @param integer $nbChart
     * @return Group
     */
    public function setNbChart(int $nbChart)
    {
        $this->nbChart = $nbChart;

        return $this;
    }

    /**
     * Get nbChart
     * @return integer
     */
    public function getNbChart()
    {
        return $this->nbChart;
    }

    /**
     * Set nbPost
     * @param integer $nbPost
     * @return Group
     */
    public function setNbPost(int $nbPost)
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    /**
     * Get nbPost
     * @return integer
     */
    public function getNbPost()
    {
        return $this->nbPost;
    }

    /**
     * Set nbPlayer
     * @param integer $nbPlayer
     * @return Group
     */
    public function setNbPlayer(int $nbPlayer)
    {
        $this->nbPlayer = $nbPlayer;

        return $this;
    }

    /**
     * Get nbPlayer
     * @return integer
     */
    public function getNbPlayer()
    {
        return $this->nbPlayer;
    }

    /**
     * Set Game
     * @param Game|null $game
     * @return $this
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param Chart $chart
     * @return $this
     */
    public function addChart(Chart $chart)
    {
        $this->charts[] = $chart;
        return $this;
    }

    /**
     * @param Chart $chart
     */
    public function removeChart(Chart $chart)
    {
        $this->charts->removeElement($chart);
    }

    /**
     * @return Chart[]|ArrayCollection
     */
    public function getCharts()
    {
        return $this->charts;
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
