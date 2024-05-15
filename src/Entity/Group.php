<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\GroupRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\IsDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\IsRankTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPlayerTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPostTrait;

/**
 * Group
 *
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id":"ASC",
 *          "libGroupEn" : "ASC",
 *          "libGroupFr" : "ASC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
#[ORM\Table(name:'vgr_group')]
#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\GroupListener"])]
#[ORM\Index(name: "idx_lib_group_fr", columns: ["lib_group_fr"])]
#[ORM\Index(name: "idx_lib_group_en", columns: ["lib_group_en"])]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'libGroupEn' => 'ASC',
        'libGroupFr' => 'ASC',
    ]
)]
class Group implements SluggableInterface
{
    use TimestampableEntity;
    use SluggableTrait;
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

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(name:'game_id', referencedColumnName:'id', nullable:false )]
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
