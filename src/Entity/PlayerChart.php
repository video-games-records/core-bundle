<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use VideoGamesRecords\CoreBundle\Controller\PlayerChart\BulkUpsert;
use VideoGamesRecords\CoreBundle\Controller\PlayerChart\GetLatestScores;
use VideoGamesRecords\CoreBundle\Controller\PlayerChart\GetLatestScoresDifferentGames;
use VideoGamesRecords\CoreBundle\Controller\PlayerChart\SendPicture;
use VideoGamesRecords\CoreBundle\Controller\PlayerChart\SendVideo;
use VideoGamesRecords\CoreBundle\Controller\PlayerChart\UpdatePlatform;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\LastUpdateTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbEqualTrait;

#[ORM\Table(name:'vgr_player_chart')]
#[ORM\Entity(repositoryClass: PlayerChartRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\PlayerChartListener"])]
#[ORM\UniqueConstraint(name: "unq_player_chart", columns: ["player_id", "chart_id"])]
#[ORM\Index(name: "idx_rank", columns: ["`rank`"])]
#[ORM\Index(name: "idx_point_chart", columns: ["point_chart"])]
#[ORM\Index(name: "idx_top_score", columns: ["is_top_score"])]
#[ORM\Index(name: "idx_last_update_player", columns: ["last_update", 'player_id'])]
#[DoctrineAssert\UniqueEntity(fields: ['chart', 'player'], message: "A score already exists")]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
            normalizationContext: ['groups' => [
                'player-chart:read',
                'player-chart:libs', 'player-chart-lib:read',
                'player-chart:status', 'player-chart-status:read',
                'player-chart:player', 'player:read:minimal',
                'player-chart:proof', 'proof:read',
                'proof:picture', 'picture:read',
                'proof:video', 'video:read:minimal',
                ]
            ]
        ),
        new Post(
            denormalizationContext: ['groups' => ['player-chart:insert', 'player-chart-lib:insert']],
            normalizationContext: ['groups' => [
                'chart:read', 'chart:libs', 'chart-lib:read',
                'chart-lib:type', 'chart-type:read',
                'chart:group', 'group:read',
                'chart:player-charts', 'player-chart:read', 'player-chart:chart',
                'player-chart:libs', 'player-chart-lib:read',
                'player-chart:player', 'player-chart:platform',
                'player-chart:status', 'player-chart-status:read']
            ],
            security: 'is_granted("ROLE_PLAYER")',
        ),
        new Put(
            denormalizationContext: ['groups' => ['player-chart:update', 'player-chart-lib:update']],
            normalizationContext: ['groups' => [
                'chart:read', 'chart:libs', 'chart-lib:read',
                'chart-lib:type', 'chart-type:read',
                'chart:group', 'group:read',
                'chart:player-charts', 'player-chart:read', 'player-chart:chart',
                'player-chart:libs', 'player-chart-lib:read',
                'player-chart:player', 'player-chart:platform',
                'player-chart:status', 'player-chart-status:read']
            ],
            security: 'is_granted("ROLE_PLAYER") and (object.getPlayer().getUserId() == user.getId()) and ((object.getStatus().getId() == 1) or (object.getStatus().getId() == 6))'
        ),
        new GetCollection(
            uriTemplate: '/player-charts/latest-different-games',
            controller: GetLatestScoresDifferentGames::class,
            normalizationContext: ['groups' => [
                'player-chart:read',
                'player-chart:chart', 'chart:read',
                'chart:group', 'group:read:minimal',
                'group:game', 'game:read:minimal',
                'player-chart:player', 'player:read:minimal',
            ]],
            openapi: new Model\Operation(
                summary: 'Récupère les N derniers scores postés avec des jeux différents',
                description: 'Retourne les derniers scores en s\'assurant qu\'un seul score par jeu est retourné. Utile pour afficher les activités récentes avec une diversité de jeux.',
                parameters: [
                    new Model\Parameter(
                        name: 'limit',
                        in: 'query',
                        description: 'Nombre maximum de scores à retourner (entre 1 et 100, défaut: 10)',
                        required: false,
                        schema: ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 10]
                    ),
                    new Model\Parameter(
                        name: 'refresh',
                        in: 'query',
                        description: 'Force le rafraîchissement du cache en vidant le cache existant',
                        required: false,
                        schema: ['type' => 'boolean', 'default' => false],
                        example: 'true'
                    )
                ]
            ),
        ),
        new GetCollection(
            uriTemplate: '/player-charts/latest',
            controller: GetLatestScores::class,
            normalizationContext: ['groups' => [
                'player-chart:read',
                'player-chart:libs', 'player-chart-lib:read',
                'player-chart:status', 'player-chart-status:read',
                'player-chart:chart', 'chart:read',
                'chart:group', 'group:read:minimal',
                'group:game', 'game:read:minimal',
                'player-chart:player', 'player:read:minimal',
                'player-chart:platform', 'platform:read',
            ]],
            paginationEnabled: true,
            paginationItemsPerPage: 20,
            paginationMaximumItemsPerPage: 100,
            openapi: new Model\Operation(
                summary: 'Récupère les derniers scores postés avec pagination',
                description: 'Retourne les derniers scores ordonnés par date de mise à jour décroissante avec pagination API Platform. Contrairement à l\'endpoint "different-games", celui-ci peut retourner plusieurs scores du même jeu.',
                parameters: [
                    new Model\Parameter(
                        name: 'days',
                        in: 'query',
                        description: 'Nombre de jours dans le passé pour filtrer les scores (défaut: 7, 0 = pas de limite)',
                        required: false,
                        schema: ['type' => 'integer', 'minimum' => 0, 'default' => 7]
                    ),
                    new Model\Parameter(
                        name: 'page',
                        in: 'query',
                        description: 'Numéro de page (pagination API Platform)',
                        required: false,
                        schema: ['type' => 'integer', 'minimum' => 1, 'default' => 1]
                    ),
                    new Model\Parameter(
                        name: 'itemsPerPage',
                        in: 'query',
                        description: 'Nombre d\'éléments par page (max 100)',
                        required: false,
                        schema: ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20]
                    )
                ]
            ),
        ),
        new Post(
            uriTemplate: '/player-charts/maj-platform',
            controller: UpdatePlatform::class,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                summary: 'Update score\'s platform',
                description: 'Update score\'s platform',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'idGame' => ['type' => 'integer'],
                                    'idPlatform' => ['type' => 'integer']
                                ]
                            ],
                            'example' => [
                                'idGame' => 0,
                                'idPlatform' => 0,
                            ]
                        ]
                    ])
                ),
            )
        ),
        new Post(
            uriTemplate: '/player-charts/{id}/send-picture',
            controller: SendPicture::class,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                summary: 'Update score\'s platform',
                description: 'Update score\'s platform',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => ['type' => 'object'],
                                ]
                            ],
                            'example' => [
                                'file' => 'base64file',
                            ]
                        ]
                    ])
                ),
            )
        ),
        new Post(
            uriTemplate: '/player-charts/{id}/send-video',
            controller: SendVideo::class,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                summary: 'Update score\'s platform',
                description: 'Update score\'s platform',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'url' => ['type' => 'string'],
                                ]
                            ],
                            'example' => [
                                'url' => 'string',
                            ]
                        ]
                    ])
                ),
            )
        ),
        new Post(
            uriTemplate: '/player-charts/bulk',
            controller: BulkUpsert::class,
            security: 'is_granted("ROLE_PLAYER")',
            openapi: new Model\Operation(
                summary: 'Créer ou modifier plusieurs player-charts en une seule fois',
                description: 'Permet de créer ou modifier plusieurs player-charts d\'un coup (upsert) pour éviter les deadlocks et améliorer les performances. Si un ID est fourni, l\'entité sera modifiée, sinon elle sera créée. Les messages de mise à jour des rangs sont envoyés de manière groupée à la fin.',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'playerCharts' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'chart' => ['type' => 'integer', 'description' => 'ID du chart'],
                                                'player' => ['type' => 'integer', 'description' => 'ID du player'],
                                                'status' => ['type' => 'integer', 'description' => 'ID du status (optionnel, défaut: 1)'],
                                                'platform' => ['type' => 'integer', 'description' => 'ID de la platform (optionnel)'],
                                                'libs' => [
                                                    'type' => 'array',
                                                    'description' => 'Valeurs du score',
                                                    'items' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'chartLib' => ['type' => 'integer', 'description' => 'ID du chartLib'],
                                                            'value' => ['type' => 'string', 'description' => 'Valeur du score']
                                                        ],
                                                        'required' => ['chartLib', 'value']
                                                    ]
                                                ]
                                            ],
                                            'required' => ['chart', 'player']
                                        ]
                                    ]
                                ],
                                'required' => ['playerCharts']
                            ],
                            'example' => [
                                'playerCharts' => [
                                    [
                                        'chart' => 1,
                                        'player' => 123,
                                        'status' => 1,
                                        'platform' => 2,
                                        'libs' => [
                                            ['chartLib' => 1, 'value' => '1000'],
                                            ['chartLib' => 2, 'value' => '00:30:45']
                                        ]
                                    ],
                                    [
                                        'id' => 456,
                                        'chart' => 2,
                                        'player' => 123,
                                        'status' => 2,
                                        'libs' => [
                                            ['chartLib' => 3, 'value' => '500']
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ])
                ),
            )
        ),
    ],
    normalizationContext: ['groups' => [
        'player-chart:read',
        'player-chart:libs', 'player-chart-lib:read',
        'player-chart:status', 'player-chart-status:read',
        'player-chart:player', 'player:read:minimal',
        'player-chart:chart', 'chart:read',
        'player-chart:platform', 'platform:read',
        'chart:group', 'group:read',
        'group:game', 'game:read',
        'player-chart:proof', 'proof:read',
        'proof:picture', 'picture:read',
        'proof:video', 'video:read',
    ]
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'status' => 'exact',
        'player' => 'exact',
        'platform' => 'exact',
        'chart' => 'exact',
        'chart.group' => 'exact',
        'chart.group.game' => 'exact',
        'chart.group.game.platforms' => 'exact',
        'rank' => 'exact',
        'nbEqual' => 'exact',
        'chart.libChartEn' => 'partial',
        'chart.libChartFr' => 'partial',
        'proof' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'lastUpdate' => 'DESC',
        'rank' => 'ASC',
        'pointChart' => 'DESC',
        'chart.libChartEn' => 'ASC',
        'chart.libChartFr' => 'ASC',
        'chart.group.libGroupEn' => 'ASC',
        'chart.group.libGroupFr' => 'ASC',
        'chart.group.game.libGameEn' => 'ASC',
        'chart.group.game.libGameFr' => 'ASC',
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['lastUpdate' => DateFilterInterface::EXCLUDE_NULL])]
#[ApiFilter(RangeFilter::class, properties: ['chart.nbPost', 'rank', 'pointChart'])]
#[ApiFilter(ExistsFilter::class, properties: ['proof', 'proof.picture', 'proof.video'])]
#[ApiResource(
    uriTemplate: '/players/{id}/charts',
    operations: [ new GetCollection() ],
    uriVariables: [
        'id' => new Link(toProperty: 'player', fromClass: Player::class),
    ],
    normalizationContext: ['groups' =>
        [ 'player-chart:read',
          'player-chart:libs', 'player-chart-lib:read',
          'player-chart:status', 'player-chart-status:read',
          'player-chart:chart', 'chart:read',
          'chart:group', 'group:read:minimal',
          'group:game', 'game:read:minimal',
          'player-chart:proof', 'proof:read',
          'proof:picture', 'picture:read',
          'proof:video', 'video:read',]
    ],
    order: ['lastUpdate' => 'DESC'],
)]
class PlayerChart
{
    use TimestampableEntity;
    use NbEqualTrait;
    use LastUpdateTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(name: '`rank`', nullable: true)]
    private ?int $rank = null;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointChart = 0;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointPlatform = 0;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $isTopScore = false;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $dateInvestigation = null;

    #[ORM\ManyToOne(targetEntity: Chart::class, inversedBy: 'playerCharts', fetch: 'EAGER')]
    #[ORM\JoinColumn(name:'chart_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Chart $chart;

    #[ORM\ManyToOne(targetEntity: Player::class, inversedBy: 'playerCharts')]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false)]
    private Player $player;

    #[ORM\OneToOne(targetEntity: Proof::class, inversedBy: 'playerChart')]
    #[ORM\JoinColumn(name:'proof_id', referencedColumnName:'id', nullable:true, onDelete:'SET NULL')]
    private ?Proof $proof = null;

    #[ORM\ManyToOne(targetEntity: PlayerChartStatus::class, inversedBy: 'playerCharts')]
    #[ORM\JoinColumn(name:'status_id', referencedColumnName:'id', nullable:false)]
    private PlayerChartStatus $status;

    #[ORM\ManyToOne(targetEntity: Platform::class)]
    #[ORM\JoinColumn(name:'platform_id', referencedColumnName:'id', nullable:true)]
    private ?Platform $platform = null;

    /**
     * @var Collection<int, PlayerChartLib>
     */
    #[ORM\OneToMany(
        mappedBy: 'playerChart',
        targetEntity: PlayerChartLib::class,
        cascade: ['persist', 'remove'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    private Collection $libs;

    public function __construct()
    {
        $this->libs = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s # %s [%s]', $this->getChart()->getDefaultName(), $this->getPlayer()->getPseudo(), $this->id);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setRank(int $rank): void
    {
        $this->rank = $rank;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setPointChart(int $pointChart): void
    {
        $this->pointChart = $pointChart;
    }

    public function getPointChart(): int
    {
        return $this->pointChart;
    }

    public function setPointPlatform(int $pointPlatform): void
    {
        $this->pointPlatform = $pointPlatform;
    }

    public function getPointPlatform(): ?int
    {
        return $this->pointPlatform;
    }

    public function getIsTopScore(): bool
    {
        return $this->isTopScore;
    }

    public function setIsTopScore(bool $isTopScore): void
    {
        $this->isTopScore = $isTopScore;
    }

    public function setDateInvestigation(?DateTime $dateInvestigation = null): void
    {
        $this->dateInvestigation = $dateInvestigation;
    }

    public function getDateInvestigation(): ?DateTime
    {
        return $this->dateInvestigation;
    }

    public function setChart(Chart $chart): void
    {
        $this->chart = $chart;
    }

    public function getChart(): Chart
    {
        return $this->chart;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    public function setProof(?Proof $proof = null): void
    {
        $this->proof = $proof;
    }

    public function getProof(): ?Proof
    {
        return $this->proof;
    }

    public function setPlatform(?Platform $platform = null): void
    {
        $this->platform = $platform;
    }

    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    public function setStatus(PlayerChartStatus $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): PlayerChartStatus
    {
        return $this->status;
    }

    public function addLib(PlayerChartLib $lib): void
    {
        $lib->setPlayerChart($this);
        $this->libs[] = $lib;
    }

    public function removeLib(PlayerChartLib $lib): void
    {
        $this->libs->removeElement($lib);
    }

    /**
     * @return Collection<int, PlayerChartLib>
     */
    public function getLibs(): Collection
    {
        return $this->libs;
    }


    public function getValuesAsString(): string
    {
        $values = [];
        foreach ($this->getLibs() as $lib) {
            $values[] = $lib->getValue();
        }
        return implode('|', $values);
    }

    public function getUrl(): string
    {
        return sprintf(
            '%s-game-g%d/%s-group-g%d/%s-chart-c%d/pc-%d/index',
            $this->getChart()->getGroup()->getGame()->getSlug(),
            $this->getChart()->getGroup()->getGame()->getId(),
            $this->getChart()->getGroup()->getSlug(),
            $this->getChart()->getGroup()->getId(),
            $this->getChart()->getSlug(),
            $this->getChart()->getId(),
            $this->getId()
        );
    }
}
