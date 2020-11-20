<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

/**
 * Group
 *
 * @ORM\Table(name="vgr_group")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GroupRepository")
 * @ApiResource(attributes={"order"={"translations.name": "ASC"}})
 * @method GroupTranslation translate(string $locale, bool $fallbackToDefault)
 */
class Group implements SluggableInterface, TimestampableInterface, TranslatableInterface
{
    use TimestampableTrait;
    use TranslatableTrait;
    use SluggableTrait;

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", mappedBy="group")
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
    public function getDefaultName()
    {
        return $this->translate('en', false)->getName();
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
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
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
