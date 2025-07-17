<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository;

#[ORM\Table(name:'vgr_player_badge')]
#[ORM\Entity(repositoryClass: PlayerBadgeRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\PlayerBadgeListener"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Put(
            denormalizationContext: ['groups' => ['player-badge:update']],
            security: 'object.getPlayer().getUserId() == user.getId()'
        ),
    ],
    normalizationContext: ['groups' => [
        'player-badge:read',
        'player-badge:badge', 'badge:read']
    ],
)]
class PlayerBadge implements BadgeInterface
{
    use TimestampableEntity;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $endedAt = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private ?int $mbOrder = null;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Player $player;

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

    public function setEndedAt(DateTime $endedAt): void
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


    public function setBadge(Badge $badge): void
    {
        $this->badge = $badge;
    }

    public function getBadge(): Badge
    {
        return $this->badge;
    }

    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function __toString(): string
    {
        return sprintf('%s # %s ', $this->getPlayer()->getPseudo(), $this->getBadge()->__toString());
    }
}
