<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Odm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Odm\Filter\RangeFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\GroupFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use VideoGamesRecords\CoreBundle\Controller\PlayerChart\SendPicture;
use VideoGamesRecords\CoreBundle\Controller\PlayerChart\SendVideo;
use VideoGamesRecords\CoreBundle\Controller\PlayerChart\UpdatePlatform;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\LastUpdateTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbEqualTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerTrait;

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
                'player-chart:player', 'player:read',
                'player-chart:proof', 'proof:read',
                'proof:picture', 'picture:read',
                'proof:video', 'video:read',
                ]
            ]
        ),
        new Post(
            denormalizationContext: ['groups' => ['player-chart:insert', 'player-chart-lib:insert']],
            security: 'is_granted("ROLE_PLAYER")'
        ),
        new Put(
            denormalizationContext: ['groups' => ['player-chart:update', 'player-chart-lib:update']],
            normalizationContext: ['groups' => [
                'player-chart:read',
                'player-chart:status', 'player-chart-status:read',
                'player-chart:platform']
            ],
            security: 'is_granted("ROLE_PLAYER") and (object.getPlayer().getUserId() == user.getId()) and ((object.getStatus().getId() == 1) or (object.getStatus().getId() == 6))'
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
    ],
    normalizationContext: ['groups' => ['player-chart:read', 'player-chart:libs', 'player-chart:status']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'status' => 'exact',
        'player' => 'exact',
        'chart' => 'exact',
        'chart.group' => 'exact',
        'chart.group.game' => 'exact',
        'chart.group.game.platforms' => 'exact',
        'rank' => 'exact',
        'nbEqual' => 'exact',
        'chart.libChartEn' => 'partial',
        'chart.libChartFr' => 'partial',
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
#[ApiFilter(
    GroupFilter::class,
    arguments: [
        'parameterName' => 'groups',
        'overrideDefaultGroups' => true,
        'whitelist' => [
            'player-chart:read',
            'player-chart:libs', 'player-chart-lib:read',
            'player-chart:status', 'player-chart-status:read',
            'player-chart:platform', 'platform:read',
            'player-chart:player', 'player:read',
            'player:country', 'country:read',
            'player-chart:chart', 'chart:read',
            'chart:group', 'group:read',
            'group:game', 'game:read',
            'player-chart:proof', 'proof:read',
            'proof:picture', 'picture.read',
            'proof:video', 'video.read',
        ]
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['lastUpdate' => DateFilterInterface::EXCLUDE_NULL])]
#[ApiFilter(RangeFilter::class, properties: ['chart.nbPost', 'rank', 'pointChart'])]
#[ApiFilter(ExistsFilter::class, properties: ['proof', 'proof.picture"', 'proof.video'])]
class PlayerChart
{
    use PlayerTrait;
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

    public function setDateInvestigation(DateTime $dateInvestigation = null): void
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

    public function setProof(Proof $proof = null): void
    {
        $this->proof = $proof;
    }

    public function getProof(): ?Proof
    {
        return $this->proof;
    }

    public function setPlatform(Platform $platform = null): void
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
