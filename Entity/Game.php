<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Entity\BadgeInterface as Badge;
use Eko\FeedBundle\Item\Writer\ItemInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * Game
 *
 * @ORM\Table(name="vgr_game", indexes={@ORM\Index(name="idxStatus", columns={"status"}), @ORM\Index(name="idxEtat", columns={"etat"}), @ORM\Index(name="idxSerie", columns={"idSerie"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameRepository")
 * @ApiResource(attributes={"order"={"translations.name"}})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "status": "exact",
 *          "platforms": "exact",
 *          "playerGame.player": "exact",
 *          "groups.charts.lostPositions.player": "exact",
 *          "translations.name" : "partial"
 *      }
 * )
 * @ApiFilter(DateFilter::class, properties={"publishedAt": DateFilter::INCLUDE_NULL_BEFORE_AND_AFTER})
 * @ApiFilter(GroupFilter::class, arguments={"parameterName": "groups", "overrideDefaultGroups": true, "whitelist": {"game.read.mini","game.list","game.platforms","platform.read"}})
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id":"ASC",
 *          "translations.name" : "ASC",
 *          "publishedAt": "DESC",
 *          "nbChart": "DESC",
 *          "nbPost": "DESC",
 *          "nbPlayer": "DESC"
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class Game implements ItemInterface, SluggableInterface, TimestampableInterface, TranslatableInterface
{
    use TimestampableTrait;
    use TranslatableTrait;
    use SluggableTrait;

    const STATUS_ACTIVE = 'ACTIF';
    const STATUS_INACTIVE = 'INACTIF';

    const ETAT_INIT = 'CREATION';
    const ETAT_CHART = 'RECORD';
    const ETAT_PICTURE = 'IMAGE';
    const ETAT_END = 'FINI';


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
     * @Assert\Length(max="200")
     * @ORM\Column(name="picture", type="string", length=200, nullable=true)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_INACTIVE;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", nullable=false)
     */
    private $etat = self::ETAT_INIT;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolDlc", type="boolean", nullable=false, options={"default":0})
     */
    private $boolDlc = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolRanking", type="boolean", nullable=true, options={"default":1})
     */
    private $boolRanking = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolMaj", type="boolean", nullable=false, options={"default":0})
     */
    private $boolMaj = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChart", type="integer", nullable=false, options={"default":0})
     */
    private $nbChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPost", type="integer", nullable=false, options={"default":0})
     */
    private $nbPost = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPlayer", type="integer", nullable=false, options={"default":0})
     */
    private $nbPlayer = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbTeam", type="integer", nullable=false, options={"default":0})
     */
    private $nbTeam = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="ordre", type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @var Serie
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSerie", referencedColumnName="id")
     * })
     */
    private $serie;

    /**
     * @var Badge
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\BadgeInterface")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="id")
     * })
     */
    private $badge;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group", mappedBy="game", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $groups;


    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\GameTopic", mappedBy="game", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $topics;


    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Video", mappedBy="game", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $videos;

    /**
     * @ORM\ManyToMany(targetEntity="Platform")
     * @ORM\JoinTable(name="vgr_game_platform",
     *      joinColumns={@ORM\JoinColumn(name="idGame", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idPlatform", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"libPlatform" = "ASC"})
     */
    private $platforms;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerGame", mappedBy="game")
     */
    private $playerGame;


    private $link;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->topics = new ArrayCollection();
        $this->platforms = new ArrayCollection();
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
     * Set id
     *
     * @param integer $id
     * @return Game
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
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
     * @param string $rules
     * @return $this
     */
    public function setRules($rules)
    {
        $this->translate(null, false)->setRules($rules);

        return $this;
    }

    /**
     * @return string
     */
    public function getRules()
    {
        return $this->translate(null, false)->getRules();
    }


    /**
     * Set picture
     *
     * @param string $picture
     * @return Game
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Game
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set etat
     *
     * @param string $etat
     * @return Game
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set dateActivation
     *
     * @param DateTime $pubishedAt
     * @return Game
     */
    public function setPublishedAt($pubishedAt)
    {
        $this->publishedAt = $pubishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set boolDlc
     *
     * @param boolean $boolDlc
     * @return Game
     */
    public function setBoolDlc($boolDlc)
    {
        $this->boolDlc = $boolDlc;

        return $this;
    }

    /**
     * Get boolDlc
     *
     * @return boolean
     */
    public function getBoolDlc()
    {
        return $this->boolDlc;
    }

    /**
     * Set boolRanking
     *
     * @param boolean $boolRanking
     * @return Game
     */
    public function setBoolRanking($boolRanking)
    {
        $this->boolRanking = $boolRanking;

        return $this;
    }

    /**
     * Get boolRanking
     *
     * @return boolean
     */
    public function getBoolRanking()
    {
        return $this->boolRanking;
    }

    /**
     * Set boolMaj
     *
     * @param boolean $boolMaj
     * @return Game
     */
    public function setBoolMaj($boolMaj)
    {
        $this->boolMaj = $boolMaj;

        return $this;
    }

    /**
     * Get boolMaj
     *
     * @return boolean
     */
    public function getBoolMaj()
    {
        return $this->boolMaj;
    }

    /**
     * Set nbChart
     *
     * @param integer $nbChart
     * @return Game
     */
    public function setNbChart($nbChart)
    {
        $this->nbChart = $nbChart;

        return $this;
    }

    /**
     * Get nbChart
     *
     * @return integer
     */
    public function getNbChart()
    {
        return $this->nbChart;
    }

    /**
     * Set nbPost
     *
     * @param integer $nbPost
     * @return Game
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
     * Set nbPlayer
     *
     * @param integer $nbPlayer
     * @return Game
     */
    public function setNbPlayer($nbPlayer)
    {
        $this->nbPlayer = $nbPlayer;

        return $this;
    }

    /**
     * Get nbPlayer
     *
     * @return integer
     */
    public function getNbPlayer()
    {
        return $this->nbPlayer;
    }

    /**
     * Set nbTeam
     *
     * @param integer $nbTeam
     * @return Game
     */
    public function setNbTeam($nbTeam)
    {
        $this->nbTeam = $nbTeam;

        return $this;
    }

    /**
     * Get nbTeam
     *
     * @return integer
     */
    public function getNbTeam()
    {
        return $this->nbTeam;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     * @return Game
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set serie
     *
     * @param Serie $serie
     * @return Game
     */
    public function setSerie(Serie $serie = null)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get idSerie
     *
     * @return Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set badge
     *
     * @param Badge $badge
     * @return Game
     */
    public function setBadge(Badge $badge = null)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get idBadge
     *
     * @return Badge
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param Group $group
     * @return $this
     */
    public function addGroup(Group $group)
    {
        $group->setGame($this);
        $this->groups[] = $group;
        return $this;
    }

    /**
     * @param Group $group
     */
    public function removeGroup(Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return mixed
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * @return mixed
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * @param Platform $platform
     * @return $this
     */
    public function addPlatform(Platform $platform)
    {
        $this->platforms[] = $platform;
        return $this;
    }

    /**
     * @param Platform $platform
     */
    public function removePlatform(Platform $platform)
    {
        $this->groups->removeElement($platform);
    }

    /**
     * @return mixed
     */
    public function getPlatforms()
    {
        return $this->platforms;
    }

    /**
     * @return mixed
     */
    public function getPlayerGame()
    {
        return $this->playerGame;
    }

    /**
     * @return array
     */
    public static function getStatusChoices()
    {
        return [
            self::STATUS_ACTIVE => self::STATUS_ACTIVE,
            self::STATUS_INACTIVE => self::STATUS_INACTIVE,
        ];
    }

    /**
     * @return array
     */
    public static function getEtatsChoices()
    {
        return [
            self::ETAT_INIT => self::ETAT_INIT,
            self::ETAT_CHART => self::ETAT_CHART,
            self::ETAT_PICTURE => self::ETAT_PICTURE,
            self::ETAT_END => self::ETAT_END,
        ];
    }


    /**
     * Set link
     *
     * @param string $link
     * @return string
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getFeedItemTitle()
    {
        return 'New Game: ' . $this->getName();
    }

    /**
     * @return string
     */
    public function getFeedItemDescription()
    {
        return null;
    }

    /**
     * @return DateTime
     */
    public function getFeedItemPubDate()
    {
        return $this->getPublishedAt();
    }

    /**
     * @return string
     */
    public function getFeedItemLink()
    {
        return $this->getLink();
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

    /**
     * @return string
     */
    public function getUrl()
    {
        return sprintf(
            '%s-game-g%d/index',
            $this->getSlug(),
            $this->getId()
        );
    }
}
