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
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Platform\Autocomplete;
use VideoGamesRecords\CoreBundle\Controller\Platform\Player\GetRankingPoints;
use VideoGamesRecords\CoreBundle\Repository\PlatformRepository;

#[ORM\Table(name:'vgr_platform')]
#[ORM\Entity(repositoryClass: PlatformRepository::class)]
#[ApiResource(
    order: ['name' => 'ASC'],
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
        ),
        new Get(),
        new Get(
            uriTemplate: '/platforms/{id}/player-ranking-points',
            controller: GetRankingPoints::class,
            normalizationContext: ['groups' => [
                'player-platform:read',
                'player-platform:player', 'player:read:minimal',
                'player:team', 'team:read:minimal',
                'player:country', 'country:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the player points leaderboard',
                description: 'Retrieves the player points leaderboard'
            ),
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
class Platform
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\NotNull]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: false)]
    private string $name = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    #[ORM\Column(length: 30, nullable: false)]
    private string $picture = 'bt_default.png';

    #[Assert\Length(max: 30)]
    #[ORM\Column(length: 30, nullable: false)]
    private string $status = 'INACTIF';

    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ['name'])]
    protected string $slug;


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
        return sprintf('%s [%s]', $this->name, $this->id);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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

    public function getSlug(): string
    {
        return $this->slug;
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
}
