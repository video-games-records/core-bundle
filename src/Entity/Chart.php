<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Chart\GetFormData;
use VideoGamesRecords\CoreBundle\Controller\Chart\Player\GetRanking;
use VideoGamesRecords\CoreBundle\Controller\Chart\Player\GetRankingDisabled;
use VideoGamesRecords\CoreBundle\Controller\Chart\Player\GetRankingPoints as PlayerGetRankingPoints;
use VideoGamesRecords\CoreBundle\Controller\Chart\Team\GetRankingPoints as TeamGetRankingPoints;
use VideoGamesRecords\CoreBundle\Repository\ChartRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\IsDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPostTrait;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;

#[ORM\Table(name:'vgr_chart')]
#[ORM\Entity(repositoryClass: ChartRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\ChartListener"])]
#[ORM\Index(name: "idx_lib_chart_fr", columns: ["lib_chart_fr"])]
#[ORM\Index(name: "idx_lib_chart_en", columns: ["lib_chart_en"])]
#[ORM\Index(name: "idx_status_player", columns: ["status_player"])]
#[ORM\Index(name: "idx_status_team", columns: ["status_team"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
            normalizationContext: ['groups' => ['chart:read', 'chart:libs', 'chart-lib:read']]
        ),
        new Get(
            uriTemplate: '/charts/{id}/form-data',
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
                summary: 'Fetch chart form data',
                description: 'Fetch chart form data'
            ),
        ),
        new Get(
            uriTemplate: '/charts/{id}/player-ranking',
            controller: GetRanking::class,
            normalizationContext: ['groups' => [
                'player-chart:read',
                'player-chart:player', 'player:read', 'player:team', 'team-read', 'player:country', 'country:read',
                'player-chart:platform', 'platform:read',
                'player-chart:status', 'player-chart-status:read',
                'player-chart:proof', 'proof:read', 'prof:video', 'video-read', 'proof:picture', 'picture:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the player chart leaderboard',
                description: 'Retrieves the player chart leaderboard'
            ),
        ),
        new Get(
            uriTemplate: '/charts/{id}/player-ranking-disabled',
            controller: GetRankingDisabled::class,
            normalizationContext: ['groups' => [
                'player-chart:read',
                'player-chart:player', 'player:read', 'player:team', 'team-read', 'player:country', 'country:read',
                'player-chart:platform', 'platform;read',
                'player-chart:status', 'player-chart-status:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the chart disabled scores',
                description: 'Retrieves the chart disabled scores',
            ),
        ),
        new Get(
            uriTemplate: '/charts/{id}/team-ranking-points',
            controller: TeamGetRankingPoints::class,
            normalizationContext: ['groups' => [
                'team-chart:read', 'team-chart:team', 'team:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the team chart leaderboard',
                description: 'Retrieves the team chart leaderboard'
            ),
            openapiContext: [
                'parameters' => [
                    [
                        'name' => 'maxRank',
                        'in' => 'query',
                        'type' => 'integer',
                        'required' => false
                    ]
                ]
            ]
        ),
        new Get(
            uriTemplate: '/charts/{id}/player-ranking-points',
            controller: PlayerGetRankingPoints::class,
            normalizationContext: ['groups' => [
                'player-chart:read', 'player-chart:player', 'player:read',
                'player-team', 'team:read',
                'player-country', 'country:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the team chart leaderboard',
                description: 'Retrieves the team chart leaderboard'
            ),
            openapiContext: [
                'parameters' => [
                    [
                        'name' => 'maxRank',
                        'in' => 'query',
                        'type' => 'integer',
                        'required' => false
                    ],
                    [
                        'name' => 'idTeam',
                        'in' => 'query',
                        'type' => 'integer',
                        'required' => false
                    ]
                ]
            ]
        ),
    ],
    normalizationContext: ['groups' => ['chart:read']]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'libChartEn' => 'ASC',
        'libChartFr' => 'ASC',
    ]
)]
class Chart implements SluggableInterface
{
    use TimestampableEntity;
    use SluggableTrait;
    use NbPostTrait;
    use IsDlcTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $libChartEn = '';

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $libChartFr = '';

    #[ORM\Column(length: 30, nullable: false, options: ['default' => 'NORMAL'])]
    private string $statusPlayer = ChartStatus::NORMAL;

    #[ORM\Column(length: 30, nullable: false, options: ['default' => 'NORMAL'])]
    private string $statusTeam = ChartStatus::NORMAL;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $isProofVideoOnly = false;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'charts')]
    #[ORM\JoinColumn(name:'group_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Group $group;

    /**
     * @var Collection<int, ChartLib>
     */
    #[ORM\OneToMany(targetEntity: ChartLib::class, cascade:['persist', 'remove'], mappedBy: 'chart', orphanRemoval: true)]
    private Collection $libs;

    /**
     * @var Collection<int, PlayerChart>
     */
    #[ORM\OneToMany(targetEntity: PlayerChart::class, mappedBy: 'chart', fetch: 'EXTRA_LAZY')]
    private Collection $playerCharts;

    /**
     * Shortcut to playerChart.rank = 1
     */
    private ?PlayerChart $playerChart1 = null;

    /**
     * Shortcut to playerChart.player = player
     */
    private ?PlayerChart $playerChartP = null;

    /**
     * @var Collection<int, Proof>
     */
    #[ORM\OneToMany(targetEntity: Proof::class, cascade:['persist', 'remove'], mappedBy: 'chart', orphanRemoval: true)]
    private Collection $proofs;

    /**
     * @var Collection<LostPosition>
     */
    #[ORM\OneToMany(targetEntity: LostPosition::class, mappedBy: 'chart')]
    private Collection $lostPositions;

    public function __construct()
    {
        $this->libs = new ArrayCollection();
        $this->playerCharts = new ArrayCollection();
        $this->lostPositions = new ArrayCollection();
        $this->proofs = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    public function getDefaultName(): ?string
    {
        return $this->libChartEn;
    }

    public function getName(): ?string
    {
        $locale = Locale::getDefault();
        if ($locale == 'fr') {
            return $this->libChartFr;
        } else {
            return $this->libChartEn;
        }
    }

    public function getCompleteName(string $locale = 'en'): string
    {
        if ($locale == 'fr') {
            return $this->getGroup()
                    ->getGame()
                    ->getLibGameFr() . ' - ' . $this->getGroup()
                    ->getLibGroupFr() . ' - ' . $this->getLibChartFr();
        } else {
            return $this->getGroup()
                    ->getGame()
                    ->getLibGameEn() . ' - ' . $this->getGroup()
                    ->getLibGroupEn() . ' - ' . $this->getLibChartEn();
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


    public function setLibChartEn(string $libChartEn): void
    {
        $this->libChartEn = $libChartEn;
    }

    public function getLibChartEn(): string
    {
        return $this->libChartEn;
    }

    public function setLibChartFr(?string $libChartFr): void
    {
        if ($libChartFr) {
            $this->libChartFr = $libChartFr;
        }
    }

    public function getLibChartFr(): string
    {
        return $this->libChartFr;
    }

    public function setStatusPlayer(string $statusPlayer): void
    {
        $this->statusPlayer = $statusPlayer;
    }

    public function getStatusPlayer(): ChartStatus
    {
        return new ChartStatus($this->statusPlayer);
    }

    public function setStatusTeam(string $statusTeam): void
    {
        $this->statusTeam = $statusTeam;
    }

    public function getStatusTeam(): ChartStatus
    {
        return new ChartStatus($this->statusTeam);
    }

    public function getIsProofVideoOnly(): bool
    {
        return $this->isProofVideoOnly;
    }

    public function setIsProofVideoOnly(bool $isProofVideoOnly): void
    {
        $this->isProofVideoOnly = $isProofVideoOnly;
    }

    public function getPlayerCharts(): Collection
    {
        return $this->playerCharts;
    }

    public function addPlayerChart(PlayerChart $playerChart): void
    {
        $this->playerCharts->add($playerChart);
    }

    public function setGroup(Group $group = null): void
    {
        $this->group = $group;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function addLib(ChartLib $lib): void
    {
        $lib->setChart($this);
        $this->libs[] = $lib;
    }

    public function removeLib(ChartLib $lib): void
    {
        $this->libs->removeElement($lib);
    }

    public function getLibs(): Collection
    {
        return $this->libs;
    }


    public function setPlayerChart1(?PlayerChart $playerChart1): void
    {
        $this->playerChart1 = $playerChart1;
    }

    public function getPlayerChart1(): ?PlayerChart
    {
        return $this->playerChart1;
    }

    public function setPlayerChartP(?PlayerChart $playerChartP): void
    {
        $this->playerChartP = $playerChartP;
    }

    public function getPlayerChartP(): ?PlayerChart
    {
        return $this->playerChartP;
    }

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

    public function getSluggableFields(): array
    {
        return ['defaultName'];
    }
}
