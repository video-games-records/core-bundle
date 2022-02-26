<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Intl\Locale;

/**
 * Game
 *
 * @ORM\Table(name="vgr_game")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\GameListener"})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "status": "exact",
 *          "platforms": "exact",
 *          "playerGame.player": "exact",
 *          "groups.charts.lostPositions.player": "exact",
 *          "libGameEn" : "partial",
 *          "libGameFr" : "partial",
 *          "badge": "exact",
 *      }
 * )
 * @ApiFilter(DateFilter::class, properties={"publishedAt": DateFilter::INCLUDE_NULL_BEFORE_AND_AFTER})
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *          "parameterName": "groups",
 *          "overrideDefaultGroups": true,
 *          "whitelist": {"game.read.mini","game.list","game.platforms","platform.read"}
 *     }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id":"ASC",
 *          "libGameEn" : "ASC",
 *          "libGameFr" : "ASC",
 *          "publishedAt": "DESC",
 *          "nbChart": "DESC",
 *          "nbPost": "DESC",
 *          "nbPlayer": "DESC"
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class Game implements SluggableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    const STATUS_ACTIVE = 'ACTIF';
    const STATUS_INACTIVE = 'INACTIF';

    const ETAT_INIT = 'CREATION';
    const ETAT_CHART = 'RECORD';
    const ETAT_PICTURE = 'IMAGE';
    const ETAT_END = 'FINI';


    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @Assert\Length(max="255")
     * @ORM\Column(name="libGameEn", type="string", length=255, nullable=false)
     */
    private ?string $libGameEn;

    /**
     * @Assert\Length(max="255")
     * @ORM\Column(name="libGameFr", type="string", length=255, nullable=false)
     */
    private ?string $libGameFr = null;

    /**
     * @Assert\Length(max="200")
     * @ORM\Column(name="picture", type="string", length=200, nullable=true)
     */
    private ?string $picture;

    /**
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private string $status = self::STATUS_INACTIVE;

    /**
     * @ORM\Column(name="etat", type="string", nullable=false)
     */
    private string $etat = self::ETAT_INIT;

    /**
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private ?DateTime $publishedAt = null;

    /**
     * @ORM\Column(name="boolDlc", type="boolean", nullable=false, options={"default":0})
     */
    private bool $boolDlc = false;

    /**
     * @ORM\Column(name="boolRanking", type="boolean", nullable=true, options={"default":1})
     */
    private bool $boolRanking = true;

    /**
     * @ORM\Column(name="nbChart", type="integer", nullable=false, options={"default":0})
     */
    private int $nbChart = 0;

    /**
     * @ORM\Column(name="nbPost", type="integer", nullable=false, options={"default":0})
     */
    private int $nbPost = 0;

    /**
     * @ORM\Column(name="nbPlayer", type="integer", nullable=false, options={"default":0})
     */
    private int $nbPlayer = 0;

    /**
     * @ORM\Column(name="nbTeam", type="integer", nullable=false, options={"default":0})
     */
    private int $nbTeam = 0;


    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Serie", inversedBy="games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSerie", referencedColumnName="id")
     * })
     */
    private ?Serie $serie;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Badge", inversedBy="game",cascade={"persist"}))
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="id")
     * })
     */
    private ?Badge $badge;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group", mappedBy="game", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $groups;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\GameDay", mappedBy="game", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $days;


    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Video", mappedBy="game", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $videos;

    /**
     * @ORM\ManyToMany(targetEntity="Platform", inversedBy="games")
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

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\ForumInterface",cascade={"persist"})
     * @ORM\JoinColumn(name="idForum", referencedColumnName="id")
     */
    private $forum;

    /**
     * @ORM\ManyToMany(targetEntity="Rule", inversedBy="games")
     * @ORM\JoinTable(name="vgr_rule_game",
     *      joinColumns={@ORM\JoinColumn(name="idGame", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idRule", referencedColumnName="id")}
     *      )
     */
    private $rules;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->platforms = new ArrayCollection();
        $this->rules = new ArrayCollection();
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
        return $this->libGameEn;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $locale = Locale::getDefault();
        if ($locale == 'fr') {
            return $this->libGameFr;
        } else {
            return $this->libGameEn;
        }
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Game
     */
    public function setId(int $id)
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
     * @param string $libGameEn
     * @return $this
     */
    public function setLibGameEn(string $libGameEn): Game
    {
        $this->libGameEn = $libGameEn;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibGameEn(): ?string
    {
        return $this->libGameEn;
    }

    /**
     * @param string $libGameFr
     * @return $this
     */
    public function setLibGameFr(string $libGameFr): Game
    {
        $this->libGameFr = $libGameFr;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibGameFr(): ?string
    {
        return $this->libGameFr;
    }


    /**
     * Set picture
     *
     * @param string|null $picture
     * @return $this
     */
    public function setPicture(string $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Game
     */
    public function setStatus(string $status): Self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set etat
     *
     * @param string $etat
     * @return Game
     */
    public function setEtat(string $etat): Self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat(): string
    {
        return $this->etat;
    }

    /**
     * @param DateTime|null $pubishedAt
     * @return $this
     */
    public function setPublishedAt(DateTime $pubishedAt = null): Self
    {
        $this->publishedAt = $pubishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return DateTime
     */
    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    /**
     * Set boolDlc
     *
     * @param boolean $boolDlc
     * @return Game
     */
    public function setBoolDlc(bool $boolDlc): Self
    {
        $this->boolDlc = $boolDlc;

        return $this;
    }

    /**
     * Get boolDlc
     *
     * @return boolean
     */
    public function getBoolDlc(): bool
    {
        return $this->boolDlc;
    }

    /**
     * Set boolRanking
     *
     * @param boolean $boolRanking
     * @return Game
     */
    public function setBoolRanking(bool $boolRanking): Self
    {
        $this->boolRanking = $boolRanking;

        return $this;
    }

    /**
     * Get boolRanking
     *
     * @return boolean
     */
    public function getBoolRanking(): bool
    {
        return $this->boolRanking;
    }

    /**
     * Set nbChart
     *
     * @param integer $nbChart
     * @return Game
     */
    public function setNbChart(int $nbChart): Self
    {
        $this->nbChart = $nbChart;

        return $this;
    }

    /**
     * Get nbChart
     *
     * @return integer
     */
    public function getNbChart(): int
    {
        return $this->nbChart;
    }

    /**
     * Set nbPost
     *
     * @param integer $nbPost
     * @return Game
     */
    public function setNbPost(int $nbPost): Self
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    /**
     * Get nbPost
     *
     * @return integer
     */
    public function getNbPost(): int
    {
        return $this->nbPost;
    }

    /**
     * Set nbPlayer
     *
     * @param integer $nbPlayer
     * @return Game
     */
    public function setNbPlayer(int $nbPlayer): Self
    {
        $this->nbPlayer = $nbPlayer;

        return $this;
    }

    /**
     * Get nbPlayer
     *
     * @return integer
     */
    public function getNbPlayer(): int
    {
        return $this->nbPlayer;
    }

    /**
     * Set nbTeam
     *
     * @param integer $nbTeam
     * @return Game
     */
    public function setNbTeam(int $nbTeam): Self
    {
        $this->nbTeam = $nbTeam;

        return $this;
    }

    /**
     * Get nbTeam
     *
     * @return integer
     */
    public function getNbTeam(): int
    {
        return $this->nbTeam;
    }

    /**
     * Set Serie
     * @param Serie|null $serie
     * @return $this
     */
    public function setSerie(Serie $serie = null): Self
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get idSerie
     *
     * @return Serie
     */
    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    /**
     * Set badge
     *
     * @param $badge
     * @return Game
     */
    public function setBadge($badge = null): Self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get idBadge
     *
     * @return Badge
     */
    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    /**
     * @param Group $group
     * @return $this
     */
    public function addGroup(Group $group): Self
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
    public function getDays()
    {
        return $this->days;
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
    public function addPlatform(Platform $platform): Self
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
     * @return ForumInterface
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * @param $forum
     * @return $this
     */
    public function setForum($forum): Self
    {
        $this->forum = $forum;
        return $this;
    }
    /**
     * @return array
     */
    public static function getStatusChoices(): array
    {
        return [
            self::STATUS_ACTIVE => self::STATUS_ACTIVE,
            self::STATUS_INACTIVE => self::STATUS_INACTIVE,
        ];
    }

    /**
     * @return array
     */
    public static function getEtatsChoices(): array
    {
        return [
            self::ETAT_INIT => self::ETAT_INIT,
            self::ETAT_CHART => self::ETAT_CHART,
            self::ETAT_PICTURE => self::ETAT_PICTURE,
            self::ETAT_END => self::ETAT_END,
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

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return sprintf(
            '%s-game-g%d/index',
            $this->getSlug(),
            $this->getId()
        );
    }

    /**
     * @param Rule $rule
     * @return $this
     */
    public function addRule(Rule $rule): Self
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param Rule $rule
     */
    public function removeRule(Rule $rule)
    {
        $this->rules->removeElement($rule);
    }

    /**
     * @return mixed
     */
    public function getRules()
    {
        return $this->rules;
    }
}
