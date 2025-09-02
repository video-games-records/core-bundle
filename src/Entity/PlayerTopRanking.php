<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\PlayerTopRankingRepository;

#[ORM\Table(name:'vgr_player_top_ranking')]
#[ORM\Index(name: "idx_player_period", columns: ["player_id", "period_type", "period_value"])]
#[ORM\Index(name: "idx_period_rank", columns: ["period_type", "period_value", "rank"])]
#[ORM\Entity(repositoryClass: PlayerTopRankingRepository::class)]
#[ApiResource(
    order: ['rank' => 'ASC'],
    operations: [
        new GetCollection(),
        new Get()
    ],
    normalizationContext: ['groups' => [
        'player-top-ranking:read',
        'player-top-ranking:player', 'player:read:minimal']
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'player' => 'exact',
        'periodType' => 'exact',
        'periodValue' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['rank', 'nbPost', 'positionChange']
)]
class PlayerTopRanking
{
    use TimestampableEntity;

    public const string PERIOD_WEEK = 'week';
    public const string PERIOD_MONTH = 'month';
    public const string PERIOD_YEAR = 'year';

    public const array PERIODS = [
        self::PERIOD_WEEK,
        self::PERIOD_MONTH,
        self::PERIOD_YEAR,
    ];

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Player $player;

    #[ORM\Column(name: '`rank`', type: 'integer', nullable: false)]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $rank;

    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $nbPost = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $positionChange = null;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::PERIODS)]
    private string $periodType;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    #[Assert\NotBlank]
    private string $periodValue;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): self
    {
        $this->player = $player;
        return $this;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function setRank(int $rank): self
    {
        $this->rank = $rank;
        return $this;
    }

    public function getNbPost(): int
    {
        return $this->nbPost;
    }

    public function setNbPost(int $nbPost): self
    {
        $this->nbPost = $nbPost;
        return $this;
    }

    public function getPositionChange(): ?int
    {
        return $this->positionChange;
    }

    public function setPositionChange(?int $positionChange): self
    {
        $this->positionChange = $positionChange;
        return $this;
    }

    public function getPeriodType(): string
    {
        return $this->periodType;
    }

    public function setPeriodType(string $periodType): self
    {
        $this->periodType = $periodType;
        return $this;
    }

    public function getPeriodValue(): string
    {
        return $this->periodValue;
    }

    public function setPeriodValue(string $periodValue): self
    {
        $this->periodValue = $periodValue;
        return $this;
    }

    /**
     * Helper method to set period for a week
     */
    public function setWeekPeriod(int $year, int $week): self
    {
        $this->periodType = self::PERIOD_WEEK;
        $this->periodValue = sprintf('%d-W%02d', $year, $week);
        return $this;
    }

    /**
     * Helper method to set period for a month
     */
    public function setMonthPeriod(int $year, int $month): self
    {
        $this->periodType = self::PERIOD_MONTH;
        $this->periodValue = sprintf('%d-%02d', $year, $month);
        return $this;
    }

    /**
     * Helper method to set period for a year
     */
    public function setYearPeriod(int $year): self
    {
        $this->periodType = self::PERIOD_YEAR;
        $this->periodValue = (string) $year;
        return $this;
    }

    /**
     * Get the year from period value
     */
    public function getYear(): int
    {
        return (int) substr($this->periodValue, 0, 4);
    }

    /**
     * Get the month number (1-12) for month periods
     */
    public function getMonth(): ?int
    {
        if ($this->periodType === self::PERIOD_MONTH) {
            return (int) substr($this->periodValue, 5, 2);
        }
        return null;
    }

    /**
     * Get the week number (1-53) for week periods
     */
    public function getWeek(): ?int
    {
        if ($this->periodType === self::PERIOD_WEEK) {
            return (int) substr($this->periodValue, 6, 2);
        }
        return null;
    }

    /**
     * Check if this ranking shows an improvement (positive position change)
     */
    public function hasImproved(): bool
    {
        return $this->positionChange !== null && $this->positionChange > 0;
    }

    /**
     * Check if this ranking shows a decline (negative position change)
     */
    public function hasDeclined(): bool
    {
        return $this->positionChange !== null && $this->positionChange < 0;
    }

    /**
     * Check if this ranking position is stable (no change)
     */
    public function isStable(): bool
    {
        return $this->positionChange === 0;
    }
}