<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\ValueObject\SerieStatus;

/**
 * Serie
 * @ORM\Table(name="vgr_serie")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\SerieRepository")
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
     * @ORM\Column(name="nbGame", type="integer", nullable=false, options={"default":0})
     */
    private int $nbGame = 0;

    /**
     * @ORM\Column(name="nbChart", type="integer", nullable=false, options={"default":0})
     */
    private int $nbChart = 0;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", mappedBy="serie", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $games;

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
     * Set nbGame
     *
     * @param integer $nbGame
     * @return Serie
     */
    public function setNbGame(int $nbGame): Serie
    {
        $this->nbGame = $nbGame;

        return $this;
    }

    /**
     * Get nbGame
     *
     * @return integer
     */
    public function getNbGame(): int
    {
        return $this->nbGame;
    }

    /**
     * Set nbChart
     *
     * @param integer $nbChart
     * @return Serie
     */
    public function setNbChart(int $nbChart): Serie
    {
        $this->nbChart = $nbChart;

        return $this;
    }

    /**
     * Get nbChart
     *
     * @return integer
     */
    public function getNbChart(): int
    {
        return $this->nbChart;
    }

    /**
     * @return mixed
     */
    public function getGames()
    {
        return $this->games;
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
