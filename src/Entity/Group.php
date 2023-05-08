<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Intl\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\NbPlayerTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\NbPostTrait;

/**
 * Group
 *
 * @ORM\Table(
 *     name="vgr_group",
 *     indexes={
 *         @ORM\Index(name="idx_libGroupFr", columns={"libGroupFr"}),
 *         @ORM\Index(name="idx_libGroupEn", columns={"libGroupEn"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GroupRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\GroupListener"})
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
class Group implements SluggableInterface
{
    use TimestampableEntity;
    use SluggableTrait;
    use NbChartTrait;
    use NbPostTrait;
    use NbPlayerTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @Assert\Length(max="255")
     * @ORM\Column(name="libGroupEn", type="string", length=255, nullable=false)
     */
    private ?string $libGroupEn;

    /**
     * @Assert\Length(max="255")
     * @ORM\Column(name="libGroupFr", type="string", length=255, nullable=false)
     */
    private ?string $libGroupFr = null;

    /**
     * @ORM\Column(name="boolDlc", type="boolean", nullable=false)
     */
    private bool $boolDlc = false;


    /**
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="groups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false)
     * })
     */
    private Game $game;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", mappedBy="group",cascade={"persist"})
     */
    private Collection $charts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->charts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    /**
     * @return string
     */
    public function getDefaultName(): string
    {
        return $this->libGroupEn;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        $locale = Locale::getDefault();
        if ($locale == 'fr') {
            return $this->libGroupFr;
        } else {
            return $this->libGroupEn;
        }
    }

    /**
     * Set idGroup
     * @param integer $id
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get idGroup
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $libGroupEn
     * @return $this
     */
    public function setLibGroupEn(string $libGroupEn): Group
    {
        $this->libGroupEn = $libGroupEn;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibGroupEn(): ?string
    {
        return $this->libGroupEn;
    }

    /**
     * @param string $libGroupFr
     * @return $this
     */
    public function setLibGroupFr(string $libGroupFr): Group
    {
        $this->libGroupFr = $libGroupFr;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibGroupFr(): ?string
    {
        return $this->libGroupFr;
    }

    /**
     * Set boolDlc
     * @param bool $boolDlc
     * @return $this
     */
    public function setBoolDlc(bool $boolDlc): Group
    {
        $this->boolDlc = $boolDlc;

        return $this;
    }

    /**
     * Get boolDlc
     * @return bool
     */
    public function getBoolDlc(): bool
    {
        return $this->boolDlc;
    }


    /**
     * Set Game
     * @param Game|null $game
     * @return $this
     */
    public function setGame(Game $game = null): Group
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * @param Chart $chart
     * @return $this
     */
    public function addChart(Chart $chart): Group
    {
        $this->charts[] = $chart;
        return $this;
    }

    /**
     * @param Chart $chart
     */
    public function removeChart(Chart $chart): void
    {
        $this->charts->removeElement($chart);
    }

    /**
     * @return Collection
     */
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
