<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Validator\Constraints as Assert;
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

#[ApiResource]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'libChartEn' => 'ASC',
        'libChartFr"' => 'ASC',
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
    private string $statusPlayer = 'NORMAL';

    #[ORM\Column(length: 30, nullable: false, options: ['default' => 'NORMAL'])]
    private string $statusTeam = 'NORMAL';


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


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->libs = new ArrayCollection();
        $this->playerCharts = new ArrayCollection();
        $this->lostPositions = new ArrayCollection();
        $this->proofs = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    /**
     * @return string|null
     */
    public function getDefaultName(): ?string
    {
        return $this->libChartEn;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        $locale = Locale::getDefault();
        if ($locale == 'fr') {
            return $this->libChartFr;
        } else {
            return $this->libChartEn;
        }
    }

    /**
     * @param string $locale
     * @return string
     */
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

    /**
     * Set idChart
     *
     * @param integer $id
     * @return Chart
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get idChart
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $libChartEn
     * @return $this
     */
    public function setLibChartEn(string $libChartEn): self
    {
        $this->libChartEn = $libChartEn;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibChartEn(): string
    {
        return $this->libChartEn;
    }

    /**
     * @param string|null $libChartFr
     * @return $this
     */
    public function setLibChartFr(?string $libChartFr): self
    {
        if ($libChartFr) {
            $this->libChartFr = $libChartFr;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getLibChartFr(): string
    {
        return $this->libChartFr;
    }

    /**
     * Set statusPlayer
     *
     * @param string $statusPlayer
     * @return Chart
     */
    public function setStatusPlayer(string $statusPlayer): self
    {
        $this->statusPlayer = $statusPlayer;
        return $this;
    }

    /**
     * Get statusPlayer
     *
     * @return ChartStatus
     */
    public function getStatusPlayer(): ChartStatus
    {
        return new ChartStatus($this->statusPlayer);
    }


    /**
     * Set statusTeam
     *
     * @param string $statusTeam
     * @return Chart
     */
    public function setStatusTeam(string $statusTeam): self
    {
        $this->statusTeam = $statusTeam;
        return $this;
    }

    /**
     * Get statusTeam
     *
     * @return ChartStatus
     */
    public function getStatusTeam(): ChartStatus
    {
        return new ChartStatus($this->statusTeam);
    }

    /**
     * @return Collection
     */
    public function getPlayerCharts(): Collection
    {
        return $this->playerCharts;
    }

    /**
     * @param PlayerChart $playerChart
     * @return $this
     */
    public function addPlayerChart(PlayerChart $playerChart): self
    {
        $this->playerCharts->add($playerChart);

        return $this;
    }

    /**
     * Set group
     * @param Group|null $group
     * @return Chart
     */
    public function setGroup(Group $group = null): Chart
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @param ChartLib $lib
     * @return $this
     */
    public function addLib(ChartLib $lib): Chart
    {
        $lib->setChart($this);
        $this->libs[] = $lib;
        return $this;
    }

    /**
     * @param ChartLib $lib
     */
    public function removeLib(ChartLib $lib): void
    {
        $this->libs->removeElement($lib);
    }

    /**
     * @return Collection
     */
    public function getLibs(): Collection
    {
        return $this->libs;
    }


    /**
     * @param PlayerChart|null $playerChart1
     */
    public function setPlayerChart1(?PlayerChart $playerChart1): void
    {
        $this->playerChart1 = $playerChart1;
    }

    /**
     * @return PlayerChart|null
     */
    public function getPlayerChart1(): ?PlayerChart
    {
        return $this->playerChart1;
    }

    /**
     * @param PlayerChart|null $playerChartP
     */
    public function setPlayerChartP(?PlayerChart $playerChartP): void
    {
        $this->playerChartP = $playerChartP;
    }

    /**
     * @return PlayerChart|null
     */
    public function getPlayerChartP(): ?PlayerChart
    {
        return $this->playerChartP;
    }

    /**
     * @return string
     */
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


    /**
     * Returns an array of the fields used to generate the slug.
     *
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['defaultName'];
    }
}
