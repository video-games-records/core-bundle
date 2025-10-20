<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Enum\BadgeType;
use VideoGamesRecords\CoreBundle\Repository\BadgeRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPlayerTrait;

#[ORM\Table(name:'vgr_badge')]
#[ORM\Entity(repositoryClass: BadgeRepository::class)]
#[ORM\Index(name: "idx_type", columns: ["type"])]
#[ORM\Index(name: "idx_value", columns: ["value"])]
#[ApiFilter(OrderFilter::class, properties: ['type', 'value'], arguments: ['orderParameterName' => 'order'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new GetCollection(
            uriTemplate: '/badges/{id}/player-history',
            controller: 'VideoGamesRecords\CoreBundle\Controller\Badge\GetPlayerHistory',
            normalizationContext: ['groups' => ['player-badge:read', 'player-badge:player', 'player:read:minimal']],
        ),
        new GetCollection(
            uriTemplate: '/badges/{id}/team-history',
            controller: 'VideoGamesRecords\CoreBundle\Controller\Badge\GetTeamHistory',
            normalizationContext: ['groups' => ['team-badge:read', 'team-badge:team', 'team:read:minimal']],
        ),
    ],
    normalizationContext: ['groups' => ['badge:read']]
)]
class Badge
{
    use NbPlayerTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(enumType: BadgeType::class)]
    private BadgeType $type;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: false, options: ['default' => 'default.gif'])]
    private string $picture;

    #[ORM\Column(length: 100, nullable: false, options: ['default' => 0])]
    private int $value = 0;

    #[ORM\OneToOne(targetEntity: Game::class, mappedBy: "badge")]
    private ?Game $game;

    #[ORM\OneToOne(targetEntity: Serie::class, mappedBy: "badge")]
    private ?Serie $serie;

    #[ORM\OneToOne(targetEntity: Country::class, mappedBy: "badge")]
    private ?Country $country;

    #[ORM\OneToOne(targetEntity: Platform::class, mappedBy: "badge")]
    private ?Platform $platform;


    public function __toString()
    {
        return sprintf('%s / %s [%s]', $this->getType()->value, $this->getPicture(), $this->getId());
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setType(BadgeType $type): void
    {
        $this->type = $type;
    }

    public function getType(): BadgeType
    {
        return $this->type;
    }

    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }


    public function majValue(): void
    {
        if (BadgeType::MASTER !== $this->type) {
            return;
        }
        if (0 === $this->getNbPlayer()) {
            $this->value = 0;
        } else {
            $this->value = (int) floor(
                100 * (6250 * (-1 / (100 + $this->getGame()
                                ->getNbPlayer() - $this->nbPlayer) + 0.0102) / (pow($this->nbPlayer, 1 / 3)))
            );
        }
    }

    public function isTypeMaster(): bool
    {
        return $this->type === BadgeType::MASTER;
    }
}
