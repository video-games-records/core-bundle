<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Serializer\Filter\GroupFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;
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
use VideoGamesRecords\CoreBundle\Traits\Entity\NbGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbMasterBadgeTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPlayerTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointBadgeTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankCupTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointBadgeTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;


#[ORM\Table(name:'vgr_team')]
#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\TeamListener"])]
#[ApiResource(order: ['libTeam' => 'ASC'])]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'libTeam' => 'partial',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'libTeam' => 'ASC',
        'createdAt' => 'DESC',
        'nbGame' => 'DESC',
        'pointGame' => 'DESC',
        'rankPointGame' => 'ASC',
    ]
)]
#[ApiFilter(
    GroupFilter::class,
    arguments: [
        'parameterName' => 'groups',
        'overrideDefaultGroups' => true,
        'whitelist' => [
            'team.rank.pointChart.read',
            'team.rank.pointGame',
            'team.rank.medal',
            'team.rank.cup',
            'team.rank.badge',
            'team.players',
        ]
    ]
)]
class Team implements SluggableInterface
{
    use TimestampableEntity;
    use SluggableTrait;
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
    use RankPointGameTrait;
    use PointGameTrait;
    use RankPointChartTrait;
    use PointChartTrait;
    use AverageChartRankTrait;
    use AverageGameRankTrait;
    use NbPlayerTrait;
    use NbGameTrait;
    use NbMasterBadgeTrait;

    const STATUS_OPENED = 'OPENED';
    const STATUS_CLOSED = 'CLOSED';

    const NUM_ITEMS = 20;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5,max: 50)]
    #[ORM\Column(length: 50, nullable: false)]
    private string $libTeam;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2,max: 4)]
    #[ORM\Column(length: 10, nullable: false)]
    private string $tag;

    #[Assert\Length(max: 255)]
    #[Assert\Url(protocols: ['https', 'http'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siteWeb;

    #[Assert\Length(max: 30)]
    #[ORM\Column(length: 30, nullable: false, options: ['default' => 'default.png'])]
    private string $logo = 'default.png';


    #[ORM\Column(type: 'text', length: 30, nullable: true)]
    private ?string $presentation;


    #[Assert\Choice(choices: ['CLOSED', 'OPENED'])]
    #[ORM\Column(length: 30, nullable: false)]
    private string $status = self::STATUS_CLOSED;


    /**
     * @var Collection<int, Player>
     */
    #[ORM\OneToMany(targetEntity: Player::class, mappedBy: 'team')]
    #[ORM\OrderBy(["pseudo" => "ASC"])]
    private Collection $players;

    /**
     * @var Collection<int, TeamGame>
     */
    #[ORM\OneToMany(targetEntity: TeamGame::class, mappedBy: 'team')]
    private Collection $teamGame;

    /**
     * @var Collection<int, TeamGame>
     */
    #[ORM\OneToMany(targetEntity: TeamBadge::class, mappedBy: 'team')]
    private Collection $teamBadge;


    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'leader_id', referencedColumnName:'id', nullable:false )]
    private Player $leader;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->teamGame = new ArrayCollection();
        $this->teamBadge = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibTeam(), $this->id);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setLibTeam(string $libTeam): void
    {
        $this->libTeam = $libTeam;
    }

    public function getLibTeam(): string
    {
        return $this->libTeam;
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setLeader(Player $leader): void
    {
        $this->leader = $leader;
    }

    public function getLeader(): Player
    {
        return $this->leader;
    }

    public function setSiteWeb(?string $siteWeb): void
    {
        $this->siteWeb = $siteWeb;
    }


    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }


    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function setPresentation(string $presentation): void
    {
        $this->presentation = $presentation;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    /**
     * @return Collection<int, TeamGame>
     */
    public function getTeamGame(): Collection
    {
        return $this->teamGame;
    }

    /**
     * @return Collection<int, TeamBadge>
     */
    public function getTeamBadge(): Collection
    {
        return $this->teamBadge;
    }

    public function isOpened(): bool
    {
        return ($this->getStatus() == self::STATUS_OPENED);
    }

    public function getSluggableFields(): array
    {
        return ['libTeam'];
    }

    public static function getStatusChoices(): array
    {
        return [
            self::STATUS_CLOSED => self::STATUS_CLOSED,
            self::STATUS_OPENED => self::STATUS_OPENED,
        ];
    }
}
