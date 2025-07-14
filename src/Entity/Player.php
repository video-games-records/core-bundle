<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\GroupFilter;
use ApiPlatform\OpenApi\Model;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Player\Autocomplete;
use VideoGamesRecords\CoreBundle\Controller\Player\GetRankingBadge;
use VideoGamesRecords\CoreBundle\Controller\Player\GetRankingCup;
use VideoGamesRecords\CoreBundle\Controller\Player\GetRankingMedals;
use VideoGamesRecords\CoreBundle\Controller\Player\GetRankingPointChart;
use VideoGamesRecords\CoreBundle\Controller\Player\GetRankingPointGame;
use VideoGamesRecords\CoreBundle\Controller\Player\GetRankingProof;
use VideoGamesRecords\CoreBundle\Controller\Player\LostPosition\GetNbLostPosition;
use VideoGamesRecords\CoreBundle\Controller\Player\LostPosition\GetNbNewLostPosition;
use VideoGamesRecords\CoreBundle\Controller\Player\Game\GetStats as GameGetStats;
use VideoGamesRecords\CoreBundle\Controller\Player\PlayerChart\GetStats as PlayerChartGetStats;
use VideoGamesRecords\CoreBundle\Controller\Player\ProofRequest\CanAskProof;
use VideoGamesRecords\CoreBundle\Filter\PlayerSearchFilter;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\AverageChartRankTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\AverageGameRankTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank0Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank1Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank2Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank3Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank4Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank5Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\GameRank0Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\GameRank1Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\GameRank2Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\GameRank3Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbMasterBadgeTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbVideoTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerCommunicationDataTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerPersonalDataTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointBadgeTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankCupTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointBadgeTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointGameTrait;

#[ORM\Table(name:'vgr_player')]
#[ORM\Entity(repositoryClass: PlayerRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\PlayerListener"])]
#[ORM\Index(name: "idx_point_game", columns: ["point_game"])]
#[ORM\Index(name: "idx_chart_rank", columns: ["chart_rank0", "chart_rank1", "chart_rank2", "chart_rank3"])]
#[ORM\Index(name: "idx_game_rank", columns: ["game_rank0", "game_rank1", "game_rank2", "game_rank3"])]
#[ApiResource(
    order: ['pseudo' => 'ASC'],
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/players/autocomplete',
            controller: Autocomplete::class,
            normalizationContext: ['groups' => [
                'player:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves players by autocompletion',
                description: 'Retrieves players by autocompletion'
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
            uriTemplate: '/players/ranking-point-chart',
            controller: GetRankingPointChart::class,
        ),
        new GetCollection(
            uriTemplate: '/players/ranking-point-game',
            controller: GetRankingPointGame::class,
        ),
        new GetCollection(
            uriTemplate: '/players/ranking-medal',
            controller: GetRankingMedals::class,
        ),
        new GetCollection(
            uriTemplate: '/players/ranking-cup',
            controller: GetRankingCup::class,
        ),
        new GetCollection(
            uriTemplate: '/players/ranking-badge',
            controller: GetRankingBadge::class,
        ),
        new GetCollection(
            uriTemplate: '/players/ranking-proof',
            controller: GetRankingProof::class,
        ),
        new Get(),
        new Get(
            uriTemplate: '/players/{id}/nb-lost-position',
            controller: GetNbLostPosition::class,
        ),
        new Get(
            uriTemplate: '/players/{id}/nb-new-lost-position',
            controller: GetNbNewLostPosition::class,
        ),
        new Get(
            uriTemplate: '/players/{id}/can-ask-proof',
            controller: CanAskProof::class,
        ),
        new Get(
            uriTemplate: '/players/{id}/player-chart-stats',
            controller: PlayerChartGetStats::class,
            normalizationContext: ['groups' => [
                'player-chart-status:read']
            ],
        ),
        new Get(
            uriTemplate: '/players/{id}/game-stats',
            controller: GameGetStats::class,
            normalizationContext: ['groups' => [
                'player-game:read', 'player-game:game', 'game:read',
                'game:platforms', 'platform:read',
                'player-game.statuses', 'player-chart-status:read']
            ],
        ),
        new Put(
            denormalizationContext: ['groups' => ['player:update']],
            security: 'is_granted("ROLE_PLAYER") and object.getUserId() == user.getId()'
        ),
    ],
    normalizationContext: ['groups' => [
        'player:read',
        'player:team', 'team:read:minimal',
        'player:country', 'country:read',
        'player:status', 'player-status:read']
    ],
)]
#[ApiResource(
    uriTemplate: '/teams/{id}/players',
    uriVariables: [
        'id' => new Link(fromClass: Team::class, toProperty: 'team'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => ['player:read']],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'pseudo' => 'partial',
        'user.enabled' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'pseudo' => 'ASC',
        'createdAt' => 'ASC',
        'nbConnexion' => 'DESC',
        'lastLogin' => 'DESC',
        'nbForumMessage' => 'DESC',
        'nbChart' => 'DESC',
        'nbVideo' => 'DESC',
    ]
)]
#[ApiFilter(
    GroupFilter::class,
    arguments: [
        'parameterName' => 'groups',
        'overrideDefaultGroups' => true,
        'whitelist' => [
            'player:read',
            'player:team',
            'player:country',
            'country:read',
            'player:read',
            'player:team',
            'team:read',
            'player:status',
            'player-status:read',
        ]
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['lastLogin' => DateFilterInterface::EXCLUDE_NULL])]
#[ApiFilter(RangeFilter::class, properties: ['nbVideo'])]
#[ApiFilter(BooleanFilter::class, properties: ['hasDonate'])]
#[ApiFilter(PlayerSearchFilter::class)]
class Player
{
    use TimestampableEntity;
    use RankCupTrait;
    use GameRank0Trait;
    use GameRank1Trait;
    use GameRank2Trait;
    use GameRank3Trait;
    use RankMedalTrait;
    use ChartRank0Trait;
    use ChartRank1Trait;
    use ChartRank2Trait;
    use ChartRank3Trait;
    use ChartRank4Trait;
    use ChartRank5Trait;
    use RankPointBadgeTrait;
    use PointBadgeTrait;
    use RankPointChartTrait;
    use PointChartTrait;
    use RankPointGameTrait;
    use PointGameTrait;
    use AverageChartRankTrait;
    use AverageGameRankTrait;
    use PlayerCommunicationDataTrait;
    use PlayerPersonalDataTrait;
    use NbChartTrait;
    use NbChartProvenTrait;
    use NbGameTrait;
    use NbVideoTrait;
    use NbMasterBadgeTrait;

    #[ORM\Column(nullable: false)]
    private int $user_id;

    #[ApiProperty(identifier: true)]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(min: 3, max: 50)]
    #[ORM\Column(length: 50, nullable: false, unique: true)]
    private string $pseudo;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: false, options: ['default' => "default.jpg"])]
    private string $avatar = 'default.jpg';

    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gamerCard = null;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $rankProof = 0;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $rankCountry = 0;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbChartMax = 0;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbChartWithPlatform = 0;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbChartDisabled = 0;

    #[ORM\Column(nullable: true)]
    protected ?DateTime $lastLogin = null;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    protected int $nbConnexion = 0;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $boolMaj = false;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $hasDonate = false;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'players')]
    #[ORM\JoinColumn(name:'team_id', referencedColumnName:'id', nullable:true, onDelete: 'SET NULL')]
    private ?Team $team;

    #[ORM\Column(nullable: true)]
    protected ?DateTime $lastDisplayLostPosition;

    #[ORM\ManyToOne(targetEntity: PlayerStatus::class)]
    #[ORM\JoinColumn(name:'status_id', referencedColumnName:'id', nullable:false)]
    private PlayerStatus $status;

    #[ORM\Column(length: 128)]
    #[Gedmo\Slug(fields: ['pseudo'])]
    protected string $slug;


    /**
     * @var Collection<int, Proof>
     */
    #[ORM\OneToMany(targetEntity: Proof::class, mappedBy: 'playerResponding')]
    private Collection $proofRespondings;

    /**
     * @var Collection<int, PlayerGame>
     */
    #[ORM\OneToMany(targetEntity: PlayerGame::class, mappedBy: 'player')]
    private Collection $playerGame;


    public function __toString()
    {
        return sprintf('%s (%d)', $this->getPseudo(), $this->getId());
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setGamerCard(string $gamerCard): void
    {
        $this->gamerCard = $gamerCard;
    }

    public function getGamerCard(): ?string
    {
        return $this->gamerCard;
    }


    public function setRankProof(int $rankProof): void
    {
        $this->rankProof = $rankProof;
    }

    public function getRankProof(): ?int
    {
        return $this->rankProof;
    }

    public function setRankCountry(int $rankCountry): void
    {
        $this->rankCountry = $rankCountry;
    }

    public function getRankCountry(): ?int
    {
        return $this->rankCountry;
    }

    public function setNbChartMax(int $nbChartMax): void
    {
        $this->nbChartMax = $nbChartMax;
    }

    public function getNbChartMax(): int
    {
        return $this->nbChartMax;
    }

    public function setNbChartWithPlatform(int $nbChartWithPlatform): void
    {
        $this->nbChartWithPlatform = $nbChartWithPlatform;
    }

    public function getNbChartWithPlatform(): int
    {
        return $this->nbChartWithPlatform;
    }

    public function setNbChartDisabled(int $nbChartDisabled): void
    {
        $this->nbChartDisabled = $nbChartDisabled;
    }

    public function getNbChartDisabled(): int
    {
        return $this->nbChartDisabled;
    }

    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTime $time = null): void
    {
        $this->lastLogin = $time;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId($userId): Player
    {
        $this->user_id = $userId;
        return $this;
    }

    public function setTeam(?Team $team = null): void
    {
        $this->team = $team;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function getLastDisplayLostPosition(): ?DateTime
    {
        return $this->lastDisplayLostPosition;
    }


    public function setLastDisplayLostPosition(?DateTime $lastDisplayLostPosition = null): void
    {
        $this->lastDisplayLostPosition = $lastDisplayLostPosition;
    }

    public function setBoolMaj(bool $boolMaj): void
    {
        $this->boolMaj = $boolMaj;
    }

    public function getBoolMaj(): bool
    {
        return $this->boolMaj;
    }

    public function getHasDonate(): bool
    {
        return $this->hasDonate;
    }

    public function setHasDonate(bool $hasDonate): void
    {
        $this->hasDonate = $hasDonate;
    }

    public function setStatus(PlayerStatus $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): PlayerStatus
    {
        return $this->status;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return int
     */
    public function getNbConnexion(): int
    {
        return $this->nbConnexion;
    }

    public function setNbConnexion(int $nbConnexion): void
    {
        $this->nbConnexion = $nbConnexion;
    }


    public function getSluggableFields(): array
    {
        return ['pseudo'];
    }

    public function getInitial(): string
    {
        return substr($this->pseudo, 0, 1);
    }


    public function isLeader(): bool
    {
        return ($this->getTeam() !== null) && ($this->getTeam()->getLeader()->getId() === $this->getId());
    }

    public function getUrl(): string
    {
        return sprintf(
            '%s-player-p%d/index',
            $this->getSlug(),
            $this->getId()
        );
    }
}
