<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\NbGameTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\PictureTrait;
use VideoGamesRecords\CoreBundle\ValueObject\SerieStatus;

/**
 * Serie
 * @ORM\Table(name="vgr_serie")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\SerieRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\SerieListener"})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "status": "exact",
 *          "libSerie" : "partial",
 *      }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "libSerie" : "ASC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class Serie implements SluggableInterface
{
    use TimestampableEntity;
    use SluggableTrait;
    use NbChartTrait;
    use NbGameTrait;
    use PictureTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @var string
     * @Assert\Length(max="255")
     * @ORM\Column(name="libSerie", type="string", length=255, nullable=false)
     */
    private string $libSerie;

    /**
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private string $status = SerieStatus::STATUS_INACTIVE;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", mappedBy="serie", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $games;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Badge", inversedBy="serie", cascade={"persist"}))
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
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
     * Returns an array of the fields used to generate the slug.
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['defaultName'];
    }
}
