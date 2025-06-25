<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Serializer\Filter\GroupFilter;
use ApiPlatform\OpenApi\Model;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Game\Autocomplete;
use VideoGamesRecords\CoreBundle\Controller\Game\GetFormData;
use VideoGamesRecords\CoreBundle\Controller\Game\GetGameOfDay;
use VideoGamesRecords\CoreBundle\Controller\Game\GetListByLetter;
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
use VideoGamesRecords\CoreBundle\Controller\Game\Player\GetRankingPoints as PlayerGetRankingPoints;
use VideoGamesRecords\CoreBundle\Controller\Game\Player\GetRankingMedals as PlayerGetRankingMedals;
use VideoGamesRecords\CoreBundle\Controller\Game\Team\GetRankingPoints as TeamGetRankingPoints;
use VideoGamesRecords\CoreBundle\Controller\Game\Team\GetRankingPoints as TeamGetRankingMedals;

#[ORM\Table(name:'vgr_game')]
#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\GameListener"])]
#[ORM\Index(name: "idx_lib_game_fr", columns: ["lib_game_fr"])]
#[ORM\Index(name: "idx_lib_game_en", columns: ["lib_game_en"])]
#[ORM\Index(name: "status", columns: ["status"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/games/list-by-letter',
            controller: GetListByLetter::class,
            openapi: new Model\Operation(
                summary: 'Retrieves games by letter',
                description: 'Retrieves games by letter',
                parameters: [
                    new Model\Parameter(
                        name: 'letter',
                        in: 'query',
                        required: true,
                        schema: [
                            'type' => 'string',
                            'pattern' => '[a-zA-Z0]'
                        ]
                    )
                ]
            ),
            paginationEnabled: false,
            normalizationContext: ['groups' => [
                'game:read', 'game:platforms', 'platform:read']
            ],
        ),
        new GetCollection(
            uriTemplate: '/games/autocomplete',
            controller: Autocomplete::class,
            normalizationContext: ['groups' => [
                'game:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves games by autocompletion',
                description: 'Retrieves games by autocompletion'
            ),
            /*openapiContext: [
                'parameters' => [
                    [
                        'name' => 'query',
                        'in' => 'query',
                        'type' => 'string',
                        'required' => true
                    ]
                ]
            ]*/
        ),
        new GetCollection(
            uriTemplate: '/games/game-of-day',
            controller: GetGameOfDay::class,
            normalizationContext: ['groups' => [
                'game:read', 'game:platforms', 'platform:read']
            ],
        ),
        new Get(
            normalizationContext: ['groups' => [
                'game:read',
                'game:platforms', 'platform:read',
                'game:serie', 'serie:read',
                'game:rules', 'rule:read',
                'game:forum', 'forum:read'
                ]
            ]
        ),
        new Get(
            uriTemplate: '/games/{id}/form-data',
            controller: GetFormData::class,
            security: "is_granted('ROLE_PLAYER')",
            normalizationContext: ['groups' => [
                'chart:read', 'chart:libs', 'chart-lib:read',
                'chart-lib:type', 'chart-type:read',
                'chart:group', 'group:read',
                'chart:player-charts', 'player-chart:read', 'player-chart:chart',
                'player-chart:libs', 'player-chart-lib:read',
                'player-chart:player', 'player-chart:platform',
                'player-chart:status', 'player-chart-status:read']
            ],
            openapi: new Model\Operation(
                summary: 'Fetch game form data',
                description: 'Fetch game form data'
            ),
            /*openapiContext: [
                'parameters' => [
                    [
                        'name' => 'term',
                        'in' => 'query',
                        'type' => 'string',
                        'required' => false
                    ]
                ]
            ]*/
        ),
        new Get(
            uriTemplate: '/games/{id}/player-ranking-points',
            controller: PlayerGetRankingPoints::class,
            normalizationContext: ['groups' => [
                'player-game:read',
                'player-game:player', 'player:read:minimal',
                'player:team', 'team:read:minimal',
                'player:country', 'country:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the player points leaderboard',
                description: 'Retrieves the player points leaderboard'
            ),
            /*openapiContext: [
                'parameters' => [
                    [
                        'name' => 'maxRank',
                        'in' => 'query',
                        'type' => 'integer',
                        'required' => false
                    ]
                ]
            ]*/
        ),
        new Get(
            uriTemplate: '/games/{id}/player-ranking-medals',
            controller: PlayerGetRankingMedals::class,
            normalizationContext: ['groups' => [
                'player-game:read',
                'player-game:player', 'player:read',
                'player:team', 'team:read:minimal',
                'player:country', 'country:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the player medals leaderboard',
                description: 'Retrieves the player medals leaderboard'
            ),
            /*openapiContext: [
                'parameters' => [
                    [
                        'name' => 'maxRank',
                        'in' => 'query',
                        'type' => 'integer',
                        'required' => false
                    ]
                ]
            ]*/
        ),
        new Get(
            uriTemplate: '/games/{id}/team-ranking-points',
            controller: TeamGetRankingPoints::class,
            normalizationContext: ['groups' => [
                'team-game:read',
                'team-game:team', 'team:read:minimal']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the team points leaderboard',
                description: 'Retrieves the team points leaderboard'
            ),
            /*openapiContext: [
                'parameters' => [
                    [
                        'name' => 'maxRank',
                        'in' => 'query',
                        'type' => 'integer',
                        'required' => false
                    ]
                ]
            ]*/
        ),
        new Get(
            uriTemplate: '/games/{id}/team-ranking-medals',
            controller: TeamGetRankingMedals::class,
            normalizationContext: ['groups' => [
                'team-game:read',
                'team-game:team', 'team:read:minimal']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the team medals leaderboard',
                description: 'Retrieves the team medals leaderboard'
            ),
            /*openapiContext: [
                'parameters' => [
                    [
                        'name' => 'maxRank',
                        'in' => 'query',
                        'type' => 'integer',
                        'required' => false
                    ]
                ]
            ]*/
        ),
    ],
    normalizationContext: ['groups' => ['game:read', 'game:platforms', 'platform:read']]
)]
#[ApiResource(
    uriTemplate: '/platforms/{id}/games',
    uriVariables: [
        'id' => new Link(fromClass: Platform::class, toProperty: 'platforms'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => ['game:read']],
)]
#[ApiResource(
    uriTemplate: '/series/{id}/games',
    uriVariables: [
        'id' => new Link(fromClass: Serie::class, toProperty: 'serie'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => ['game:read', 'game:platforms', 'platform:read']],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
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
            'game:read',
            'game:platforms', 'platform:read',
            'game:last-score', 'player-chart:read',
            'player-chart:player', 'player:read',
            'player-chart:chart', 'chart:read',
        ]
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['publishedAt' => DateFilterInterface::INCLUDE_NULL_BEFORE_AND_AFTER])]
class Game
{
    use TimestampableEntity;
    use NbChartTrait;
    use NbPostTrait;
    use NbPlayerTrait;
    use NbTeamTrait;
    use PictureTrait;
    use NbVideoTrait;
    use IsRankTrait;
    use LastUpdateTrait;

    #[ApiProperty(identifier: true)]
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

    #[ORM\Column(length: 30, nullable: false, options: ['default' => GameStatus::CREATED])]
    private string $status = GameStatus::CREATED;

    #[ORM\Column(nullable: true)]
    private ?DateTime $publishedAt = null;


    #[ORM\ManyToOne(targetEntity: Serie::class, inversedBy: 'games')]
    #[ORM\JoinColumn(name:'serie_id', referencedColumnName:'id', nullable:true)]
    private ?Serie $serie = null;


    #[ORM\OneToOne(targetEntity: Badge::class, cascade: ['persist'], inversedBy: 'game')]
    #[ORM\JoinColumn(name:'badge_id', referencedColumnName:'id', nullable:true)]
    private ?Badge $badge = null;

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

    #[ORM\OneToOne(targetEntity: ForumInterface::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'forum_id', referencedColumnName:'id', nullable:true)]
    private $forum;

    #[ORM\OneToOne(targetEntity: PlayerChart::class)]
    #[ORM\JoinColumn(name:'last_score_id', referencedColumnName:'id', nullable:true)]
    private ?PlayerChart $lastScore;

    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ['libGameEn'])]
    protected string $slug;

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


    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->platforms = new ArrayCollection();
        $this->rules = new ArrayCollection();
        $this->playerGame = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s (%d)', $this->getName(), $this->getId());
    }

    public function getDefaultName(): string
    {
        return $this->libGameEn;
    }

    public function getName(?string $locale = null): ?string
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

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLibGameEn(string $libGameEn): void
    {
        $this->libGameEn = $libGameEn;
    }

    public function getLibGameEn(): string
    {
        return $this->libGameEn;
    }

    public function setLibGameFr(?string $libGameFr): void
    {
        if ($libGameFr) {
            $this->libGameFr = $libGameFr;
        }
    }

    public function getLibGameFr(): string
    {
        return $this->libGameFr;
    }

    public function setDownloadurl(?string $downloadUrl = null): void
    {
        $this->downloadUrl = $downloadUrl;
    }

    public function getDownloadUrl(): ?string
    {
        return $this->downloadUrl;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getGameStatus(): GameStatus
    {
        return new GameStatus($this->status);
    }

    public function getStatusAsString(): string
    {
        return $this->status;
    }

    public function setPublishedAt(?DateTime $pubishedAt = null): void
    {
        $this->publishedAt = $pubishedAt;
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    public function setSerie(?Serie $serie = null): void
    {
        $this->serie = $serie;
    }

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setBadge($badge = null): void
    {
        $this->badge = $badge;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function addGroup(Group $group): void
    {
        $group->setGame($this);
        $this->groups[] = $group;
    }

    public function removeGroup(Group $group): void
    {
        $this->groups->removeElement($group);
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addPlatform(Platform $platform): void
    {
        $this->platforms[] = $platform;
    }

    public function removePlatform(Platform $platform): void
    {
        $this->platforms->removeElement($platform);
    }

    public function getPlatforms(): ArrayCollection|Collection
    {
        return $this->platforms;
    }

    public function getForum()
    {
        return $this->forum;
    }

    public function setForum($forum): void
    {
        $this->forum = $forum;
    }

    public function getLastScore(): ?PlayerChart
    {
        return $this->lastScore;
    }

    public function setLastScore(?PlayerChart $lastScore): void
    {
        $this->lastScore = $lastScore;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getUrl(): string
    {
        return sprintf(
            '%s-game-g%d/index',
            $this->getSlug(),
            $this->getId()
        );
    }

    public function addRule(Rule $rule): void
    {
        $this->rules[] = $rule;
    }

    public function removeRule(Rule $rule): void
    {
        $this->rules->removeElement($rule);
    }

    public function getPlayerGame(): Collection
    {
        return $this->playerGame;
    }

    public function getRules(): Collection
    {
        return $this->rules;
    }
}
