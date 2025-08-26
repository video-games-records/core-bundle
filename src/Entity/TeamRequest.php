<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\TeamRequestRepository;
use VideoGamesRecords\CoreBundle\ValueObject\TeamRequestStatus;

#[ORM\Table(name:'vgr_team_request')]
#[ORM\Entity(repositoryClass: TeamRequestRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\TeamRequestListener"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            denormalizationContext: ['groups' => ['team-request:insert']],
            security: 'is_granted("ROLE_PLAYER")'
        ),
        new Put(
            denormalizationContext: ['groups' => ['team-request:update']],
            security: 'is_granted("ROLE_PLAYER") and ((object.getTeam().getLeader().getUserId() == user.getId()) or (object.getPlayer().getUserId() == user.getId()))'
        ),
    ],
    normalizationContext: ['groups' => [
        'team-request:read', 'team-request:player', 'player:read-minimal', 'team-request:team']
    ],
)]
#[ApiResource(
    uriTemplate: '/players/{id}/team_requests',
    uriVariables: [
        'id' => new Link(fromClass: Player::class, toProperty: 'player'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => [
        'team-request:read', 'team-request:team', 'team:read:minimal',]
    ],
)]
#[ApiResource(
    uriTemplate: '/teams/{id}/team_requests',
    uriVariables: [
        'id' => new Link(fromClass: Team::class, toProperty: 'team'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => [
        'team-request:read', 'team-request:player', 'player:read:minimal',]
    ],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'status' => 'exact',
    ]
)]
class TeamRequest
{
    use TimestampableEntity;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(max: 30)]
    #[ORM\Column(length: 30, nullable: false)]
    private string $status = TeamRequestStatus::ACTIVE;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name:'team_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Team $team;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Player $player;

    public function __toString()
    {
        return sprintf('%s # %s [%s]', $this->getTeam()->getLibTeam(), $this->getPlayer()->getPseudo(), $this->id);
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
        $value = new TeamRequestStatus($status);
        $this->status = $value->getValue();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTeamRequestStatus(): TeamRequestStatus
    {
        return new TeamRequestStatus($this->status);
    }

    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }
}
