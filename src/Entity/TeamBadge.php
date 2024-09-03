<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\GroupFilter;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository;

#[ORM\Table(name:'vgr_team_badge')]
#[ORM\Entity(repositoryClass: TeamBadgeRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\TeamBadgeListener"])]
#[ApiResource(
    order: ['badge.type' => 'ASC', 'badge.value' => 'ASC'],
    operations: [
        new GetCollection(),
        new Get(),
        new Put(
            denormalizationContext: ['groups' => ['team-badge:update']],
            security: 'object.getTeam().getLeader().getUserId() == user.getId()'
        ),
    ],
    normalizationContext: ['groups' => [
        'team-badge:read',
        'team-badge:badge', 'badge:read']
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'team' => 'exact',
        'badge' => 'exact',
        'badge.type' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'createdAt' => 'ASC',
        'mbOrder' => 'ASC',
    ]
)]
#[ApiFilter(
    GroupFilter::class,
    arguments: [
        'parameterName' => 'groups',
        'overrideDefaultGroups' => true,
        'whitelist' => [
            'team-badge:read',
            'team-badge:badge', 'badge:read',
            'team-badge:team', 'team:read',
            'badge:game', 'game:read',
            'badge.serie', 'serie.read',
        ]
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['endedAt' => DateFilterInterface::INCLUDE_NULL_BEFORE_AND_AFTER])]
class TeamBadge
{
    use TimestampableEntity;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $endedAt = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private ?int $mbOrder = null;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'teamBadge')]
    #[ORM\JoinColumn(name:'team_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Team $team;

    #[ORM\ManyToOne(targetEntity: Badge::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name:'badge_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Badge $badge;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEndedAt(?DateTime $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    public function getEndedAt(): ?DateTime
    {
        return $this->endedAt;
    }

    public function setMbOrder(int $mbOrder): void
    {
        $this->mbOrder = $mbOrder;
    }

    public function getMbOrder(): ?int
    {
        return $this->mbOrder;
    }

    public function setBadge($badge = null): void
    {
        $this->badge = $badge;
    }

    public function getBadge(): Badge
    {
        return $this->badge;
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
