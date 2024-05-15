<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\GameDayRepository;

#[ORM\Table(name:'vgr_game_day')]
#[ORM\Entity(repositoryClass: GameDayRepository::class)]
#[ORM\UniqueConstraint(name: "uniq_game_of_day", columns: ["day"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get()
    ],
    normalizationContext: ['groups' => ['game-day:read', 'game-day:game', 'game:read', 'game:platforms', 'platform:read']]
)]
#[ApiFilter(DateFilter::class, properties: ['day' => DateFilter::EXCLUDE_NULL])]
class GameDay
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\NotNull]
    #[ORM\Column(type: 'date', nullable: false)]
    private DateTime $day;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(name:'game_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Game $game;

    public function __toString(): string
    {
        return sprintf('%s [%s]', $this->getDay()->format('Y-m-d'), $this->id);
    }


    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDay($day): void
    {
        $this->day = $day;
    }

    public function getDay(): DateTime
    {
        return $this->day;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }
}
