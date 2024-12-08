<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Serie\Player\GetRankingPoints;
use VideoGamesRecords\CoreBundle\Controller\Serie\Player\GetRankingMedals;
use VideoGamesRecords\CoreBundle\Repository\SerieRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PictureTrait;
use VideoGamesRecords\CoreBundle\ValueObject\SerieStatus;

#[ORM\Table(name:'vgr_serie')]
#[ORM\Entity(repositoryClass: SerieRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\SerieListener"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Get(
            uriTemplate: '/series/{id}/player-ranking-points',
            controller: GetRankingPoints::class,
            normalizationContext: ['groups' => [
                'player-serie:read',
                'player-serie:player', 'player:read',
                'player:team', 'team:read',
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
            uriTemplate: '/series/{id}/player-ranking-medals',
            controller: GetRankingMedals::class,
            normalizationContext: ['groups' => [
                'player-serie:read',
                'player-serie:player', 'player:read',
                'player:team', 'team:read',
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
    ],
    normalizationContext: ['groups' => ['serie:read']]
)]
#[ApiFilter(SearchFilter::class, properties: ['status' => 'exact', 'libSerie' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['libSerie'])]
class Serie implements SluggableInterface, TranslatableInterface
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
    private string $status = SerieStatus::INACTIVE;


    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'serie')]
    private Collection $games;


    #[ORM\OneToOne(targetEntity: Badge::class, inversedBy: 'serie', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name:'badge_id', referencedColumnName:'id', nullable:true, onDelete: 'SET NULL')]
    private ?Badge $badge;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    public function getDefaultName(): string
    {
        return $this->libSerie;
    }

    public function getName(): string
    {
        return $this->libSerie;
    }

    public function setLibSerie(string $libSerie): void
    {
        $this->libSerie = $libSerie;
    }

    public function getLibSerie(): string
    {
        return $this->libSerie;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSerieStatus(): SerieStatus
    {
        return new SerieStatus($this->status);
    }

    public function getGames(): Collection
    {
        return $this->games;
    }

    public function setBadge($badge = null): void
    {
        $this->badge = $badge;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setDescription(string $description): void
    {
        $this->translate(null, false)->setDescription($description);
    }

    public function getDescription(): ?string
    {
        return $this->translate(null, false)->getDescription();
    }

    public function getDefaultDescription(): string
    {
        return $this->translate('en', false)->getDescription();
    }

    public function getSluggableFields(): array
    {
        return ['defaultName'];
    }
}
