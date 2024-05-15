<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\SerieRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PictureTrait;
use VideoGamesRecords\CoreBundle\ValueObject\SerieStatus;

#[ORM\Table(name:'vgr_serie')]
#[ORM\Entity(repositoryClass: SerieRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\SerieListener"])]
#[ApiResource]
#[ApiFilter(SearchFilter::class, properties: ['status' => 'exact', 'libSerie' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['libSerie'])]
class Serie implements SluggableInterface,TranslatableInterface
{
    use TimestampableEntity;
    use TranslatableTrait;
    use SluggableTrait;
    use NbChartTrait;
    use NbGameTrait;
    use PictureTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(name: 'libSerie', length: 255, nullable: false)]
    private string $libSerie;

    #[ORM\Column(nullable: false)]
    private string $status = SerieStatus::STATUS_INACTIVE;


    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'serie')]
    private Collection $games;


    #[ORM\OneToOne(targetEntity: Badge::class, inversedBy: 'serie', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name:'badge_id', referencedColumnName:'id', nullable:true, onDelete: 'SET NULL')]
    private ?Badge $badge;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    /**
     * @return string
     */
    public function getDefaultName(): string
    {
        return $this->libSerie;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->libSerie;
    }

    /**
     * @param string $libSerie
     * @return $this
     */
    public function setLibSerie(string $libSerie): Serie
    {
        $this->libSerie = $libSerie;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibSerie(): string
    {
        return $this->libSerie;
    }

    /**
     * Set id
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): Serie
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set status
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): Serie
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return SerieStatus
     */
    public function getStatus(): SerieStatus
    {
        return new SerieStatus($this->status);
    }

    /**
     * @return mixed
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param $badge
     */
    public function setBadge($badge = null): void
    {
        $this->badge = $badge;
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
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): Serie
    {
        $this->translate(null, false)->setDescription($description);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->translate(null, false)->getDescription();
    }

    /**
     * @return string
     */
    public function getDefaultDescription(): string
    {
        return $this->translate('en', false)->getDescription();
    }

    /**
     * Returns an array of the fields used to generate the slug.
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['defaultName'];
    }
}
