<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Serializer\Filter\GroupFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\IsRankTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\LastUpdateTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPlayerTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPostTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbTeamTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbVideoTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PictureTrait;
use VideoGamesRecords\CoreBundle\ValueObject\GameStatus;

#[ORM\Table(name:'vgr_game')]
#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\GameListener"])]
#[ORM\Index(name: "idx_lib_game_fr", columns: ["lib_game_fr"])]
#[ORM\Index(name: "idx_lib_game_en", columns: ["lib_game_en"])]
#[ORM\Index(name: "status", columns: ["status"])]
#[ApiResource]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'status' => 'exact',
        'platforms' => 'exact',
        'playerGame.player' => 'exact',
        'groups.charts.lostPositions.player' => 'exact',
        'libGameEn' => 'partial',
        'libGameFr' => 'partial',
        'badge' => 'exact',
        'serie' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'libGameEn' => 'ASC',
        'libGameFr' => 'ASC',
        'publishedAt' => 'DESC',
        'nbChart' => 'DESC',
        'nbPost' => 'DESC',
        'nbPlayer' => 'DESC',
        'nbVideo' => 'DESC',
        'lastUpdate' => 'DESC',
    ]
)]
#[ApiFilter(
    GroupFilter::class,
    arguments: [
        'parameterName' => 'groups',
        'overrideDefaultGroups' => true,
        'whitelist' => [
            'game.read',
            'game.read.mini',
            'game.list',
            'game.platforms',
            'platform.read',
            'lastScore.read',
            'playerChart.read',
            'playerChart.player',
            'playerChart.chart',
            'player.read.mini',
            'chart.read.mini',
        ]
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['publishedAt' => DateFilterInterface::INCLUDE_NULL_BEFORE_AND_AFTER])]
class Game implements SluggableInterface
{
    use TimestampableEntity;
    use SluggableTrait;
    use NbChartTrait;
    use NbPostTrait;
    use NbPlayerTrait;
    use NbTeamTrait;
    use PictureTrait;
    use NbVideoTrait;
    use IsRankTrait;
    use LastUpdateTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    protected ?int $id = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $libGameEn = '';

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $libGameFr = '';

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $downloadUrl;

    #[ORM\Column(length: 30, nullable: false, options: ['default' => GameStatus::STATUS_CREATED])]
    private string $status = GameStatus::STATUS_CREATED;

    #[ORM\Column(nullable: true)]
    private ?DateTime $publishedAt = null;


    #[ORM\ManyToOne(targetEntity: Serie::class, inversedBy: 'games')]
    #[ORM\JoinColumn(name:'serie_id', referencedColumnName:'id', nullable:true)]
    private ?Serie $serie;


    #[ORM\OneToOne(targetEntity: Badge::class, cascade: ['persist'], inversedBy: 'game')]
    #[ORM\JoinColumn(name:'badge_id', referencedColumnName:'id', nullable:true)]
    private ?Badge $badge;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\OneToMany(targetEntity: Group::class, cascade:['persist', 'remove'], mappedBy: 'game', orphanRemoval: true)]
    private Collection $groups;

    /**
     * @var Collection<int, Platform>
     */
    #[ORM\JoinTable(name: 'vgr_game_platform')]
    #[ORM\JoinColumn(name: 'game_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'platform_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Platform::class, inversedBy: 'games')]
    private Collection $platforms;



    //#[ORM\OneToOne(targetEntity: ForumInterface::class, cascade: ['persist'])]
    //#[ORM\JoinColumn(name:'forum_id', referencedColumnName:'id', nullable:true)]
    //private $forum;

    #[ORM\OneToOne(targetEntity: PlayerChart::class)]
    #[ORM\JoinColumn(name:'last_score_id', referencedColumnName:'id', nullable:true)]
    private ?PlayerChart $lastScore;

    /**
     * @var Collection<int, Rule>
     */
    #[ORM\ManyToMany(targetEntity: Rule::class, inversedBy: 'games')]
    #[ORM\JoinTable(name: 'vgr_rule_game')]
    private Collection $rules;

    /**
     * @var Collection<int, PlayerGame>
     */
    #[ORM\OneToMany(targetEntity: PlayerGame::class, mappedBy: 'game')]
    private Collection $playerGame;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->platforms = new ArrayCollection();
        $this->rules = new ArrayCollection();
        $this->playerGame = new ArrayCollection();
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
     * @param string|null $locale
     * @return string|null
     */
    public function getName(string $locale = null): ?string
    {
        if ($locale === null) {
            $locale = Locale::getDefault();
        }
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
    public function setId(int $id): Game
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $libGameEn
     * @return Game
     */
    public function setLibGameEn(string $libGameEn): Game
    {
        $this->libGameEn = $libGameEn;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibGameEn(): string
    {
        return $this->libGameEn;
    }

    /**
     * @param ?string $libGameFr
     * @return Game
     */
    public function setLibGameFr(?string $libGameFr): Game
    {
        if ($libGameFr) {
            $this->libGameFr = $libGameFr;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getLibGameFr(): string
    {
        return $this->libGameFr;
    }

    /**
     * @param string|null $downloadUrl
     * @return Game
     */
    public function setDownloadurl(string $downloadUrl = null): Game
    {
        $this->downloadUrl = $downloadUrl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDownloadUrl(): ?string
    {
        return $this->downloadUrl;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Game
     */
    public function setStatus(string $status): Game
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return GameStatus
     */
    public function getStatus(): GameStatus
    {
        return new GameStatus($this->status);
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatusAsString(): string
    {
        return $this->status;
    }

    /**
     * @param DateTime|null $pubishedAt
     * @return Game
     */
    public function setPublishedAt(DateTime $pubishedAt = null): Game
    {
        $this->publishedAt = $pubishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     * @return DateTime|null
     */
    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }


    /**
     * Set Serie
     * @param Serie|null $serie
     * @return Game
     */
    public function setSerie(Serie $serie = null): Game
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
    public function setBadge($badge = null): Game
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get idBadge
     * @return Badge|null
     */
    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    /**
     * @param Group $group
     * @return Game
     */
    public function addGroup(Group $group): Game
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
     * @param Platform $platform
     * @return Game
     */
    public function addPlatform(Platform $platform): Game
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
     * @return ForumInterface
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * @param $forum
     * @return Game
     */
    public function setForum($forum): Game
    {
        $this->forum = $forum;
        return $this;
    }

    public function getLastScore(): ?PlayerChart
    {
        return $this->lastScore;
    }

    public function setLastScore(?PlayerChart $lastScore): void
    {
        $this->lastScore = $lastScore;
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
     * @return Game
     */
    public function addRule(Rule $rule): Game
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param Rule $rule
     * @return Game
     */
    public function removeRule(Rule $rule): Game
    {
        $this->rules->removeElement($rule);
        return $this;
    }

    public function getPlaterGame()
    {
        return $this->playerGame;
    }

    /**
     * @return mixed
     */
    public function getRules()
    {
        return $this->rules;
    }
}
