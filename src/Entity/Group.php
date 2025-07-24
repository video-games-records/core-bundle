<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\OpenApi\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Group\GetCharts;
use VideoGamesRecords\CoreBundle\Controller\Group\GetFormData;
use VideoGamesRecords\CoreBundle\Controller\Group\GetTopScore;
use VideoGamesRecords\CoreBundle\Repository\GroupRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\IsDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\IsRankTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPlayerTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPostTrait;
use VideoGamesRecords\CoreBundle\Controller\Group\Player\GetRankingPoints as PlayerGetRankingPoints;
use VideoGamesRecords\CoreBundle\Controller\Group\Player\GetRankingMedals as PlayerGetRankingMedals;
use VideoGamesRecords\CoreBundle\Controller\Group\Team\GetRankingPoints as TeamGetRankingPoints;
use VideoGamesRecords\CoreBundle\Controller\Group\Team\GetRankingPoints as TeamGetRankingMedals;
use VideoGamesRecords\CoreBundle\ValueObject\GroupOrderBy;

#[ORM\Table(name:'vgr_group')]
#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\GroupListener"])]
#[ORM\Index(name: "idx_lib_group_fr", columns: ["lib_group_fr"])]
#[ORM\Index(name: "idx_lib_group_en", columns: ["lib_group_en"])]
#[ApiResource(
    paginationEnabled: false,
    operations: [
        new GetCollection(),
        new Get(),
        new Get(
            uriTemplate: '/groups/{id}/charts',
            controller: GetCharts::class,
            normalizationContext: ['groups' => [
                'chart:read']
            ],
        ),
        new Get(
            uriTemplate: '/groups/{id}/form-data',
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
        ),
        new Get(
            uriTemplate: '/groups/{id}/player-ranking-points',
            controller: PlayerGetRankingPoints::class,
            normalizationContext: ['groups' => [
                'player-group:read',
                'player-group:player', 'player:read:minimal',
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
            uriTemplate: '/groups/{id}/player-ranking-medals',
            controller: PlayerGetRankingMedals::class,
            normalizationContext: ['groups' => [
                'player-group:read',
                'player-group:player', 'player:read:minimal',
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
            uriTemplate: '/groups/{id}/team-ranking-points',
            controller: TeamGetRankingPoints::class,
            normalizationContext: ['groups' => [
                'team-group:read',
                'team-group:team', 'team:read:minimal']
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
            uriTemplate: '/groups/{id}/team-ranking-medals',
            controller: TeamGetRankingMedals::class,
            normalizationContext: ['groups' => [
                'team-group:read',
                'team-group:team', 'team:read:minimal']
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
        new Get(
            uriTemplate: '/groups/{id}/top-score',
            controller: GetTopScore::class,
            normalizationContext: ['groups' => [
                'chart:read', 'player-chart:chart',
                'player-chart:read', 'player-chart:player', 'player-chart:libs',
                'player-chart-lib:read', 'chart:top-score', 'player:read:minimal']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the top score',
                description: 'Retrieves the top score'
            ),
        ),
    ],
    normalizationContext: ['groups' => ['group:read']]
)]
#[ApiResource(
    uriTemplate: '/games/{id}/groups',
    uriVariables: [
        'id' => new Link(fromClass: Game::class, toProperty: 'game'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => ['group:read']],
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'libGroupEn' => 'ASC',
        'libGroupFr' => 'ASC',
    ]
)]
class Group
{
    use TimestampableEntity;
    use NbChartTrait;
    use NbPostTrait;
    use NbPlayerTrait;
    use IsRankTrait;
    use IsDlcTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    protected ?int $id = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $libGroupEn = '';

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $libGroupFr = '';

    #[ORM\Column(length: 30, nullable: false, options: ['default' => GroupOrderBy::NAME])]
    private string $orderBy = GroupOrderBy::NAME;

    #[ORM\Column(length: 128)]
    #[Gedmo\Slug(fields: ['libGroupEn'])]
    protected string $slug;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(name:'game_id', referencedColumnName:'id', nullable:false)]
    private Game $game;

    /**
     * @var Collection<int, Chart>
     */
    #[ORM\OneToMany(targetEntity: Chart::class, cascade:['persist'], mappedBy: 'group')]
    private Collection $charts;


    public function __construct()
    {
        $this->charts = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    public function getDefaultName(): string
    {
        return $this->libGroupEn;
    }

    public function getName(): ?string
    {
        $locale = Locale::getDefault();
        if ($locale == 'fr') {
            return $this->libGroupFr;
        } else {
            return $this->libGroupEn;
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

    public function setLibGroupEn(string $libGroupEn): void
    {
        $this->libGroupEn = $libGroupEn;
    }

    public function getLibGroupEn(): string
    {
        return $this->libGroupEn;
    }

    public function setLibGroupFr(?string $libGroupFr): void
    {
        if ($libGroupFr) {
            $this->libGroupFr = $libGroupFr;
        }
    }

    public function getLibGroupFr(): string
    {
        return $this->libGroupFr;
    }

    public function getGroupOrderBy(): GroupOrderBy
    {
        return new GroupOrderBy($this->orderBy);
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function setOrderBy(string $orderBy): void
    {
        $value = new GroupOrderBy($orderBy);
        $this->orderBy = $value->getValue();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function addChart(Chart $chart): void
    {
        $chart->setGroup($this);
        $this->charts[] = $chart;
    }

    public function removeChart(Chart $chart): void
    {
        $this->charts->removeElement($chart);
    }

    public function getCharts(): Collection
    {
        return $this->charts;
    }
}
