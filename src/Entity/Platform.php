<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Platform\Autocomplete;
use VideoGamesRecords\CoreBundle\Controller\Platform\Player\GetRankingPoints;
use VideoGamesRecords\CoreBundle\Repository\PlatformRepository;

#[ORM\Table(name:'vgr_platform')]
#[ORM\Entity(repositoryClass: PlatformRepository::class)]
#[ApiResource(
    order: ['libPlatform' => 'ASC'],
    paginationEnabled: false,
    operations: [
        new GetCollection(),
        new GetCollection(
            uriTemplate: '/platforms/autocomplete',
            controller: Autocomplete::class,
            openapi: new Model\Operation(
                summary: 'Retrieves platforms by autocompletion',
                description: 'Retrieves platforms by autocompletion'
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
        new Get(),
        new Get(
            uriTemplate: '/platforms/{id}/player-ranking-point',
            controller: GetRankingPoints::class,
            normalizationContext: ['groups' => [
                'player-platform:read',
                'player-platform:player', 'player:read',
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
    ],
    normalizationContext: ['groups' => ['platform:read']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'status' => 'exact',
        'playerPlatform.player' => 'exact',
        'games.playerGame.player' => 'exact',
    ]
)]
class Platform implements SluggableInterface
{
    use SluggableTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\NotNull]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: false)]
    private string $libPlatform = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    #[ORM\Column(length: 30, nullable: false)]
    private string $picture = 'bt_default.png';

    #[Assert\Length(max: 30)]
    #[ORM\Column(length: 30, nullable: false)]
    private string $status = 'INACTIF';

    /**
     * @var Collection<int, Game>
     */
    #[Orm\ManyToMany(targetEntity: Game::class, mappedBy: 'platforms')]
    private Collection $games;


    #[ORM\OneToOne(targetEntity: Badge::class, cascade: ['persist'], inversedBy: 'platform')]
    #[ORM\JoinColumn(name:'badge_id', referencedColumnName:'id', nullable:true)]
    private ?Badge $badge;

    /**
     * @var Collection<int, PlayerPlatform>
     */
    #[ORM\OneToMany(targetEntity: PlayerPlatform::class, mappedBy: 'platform')]
    private Collection $playerPlatform;

    public function __toString()
    {
        return sprintf('%s [%s]', $this->libPlatform, $this->id);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibPlatform(): string
    {
        return $this->libPlatform;
    }

    public function setLibPlatform(string $libPlatform): void
    {
        $this->libPlatform = $libPlatform;
    }

    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
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

    public function getPlayerPlatform(): Collection
    {
        return $this->playerPlatform;
    }

    public function getSluggableFields(): array
    {
        return ['libPlatform'];
    }
}
