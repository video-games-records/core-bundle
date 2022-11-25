<?php

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class GameStatus
{
    const STATUS_ACTIVE = 'ACTIF';
    const STATUS_INACTIVE = 'INACTIF';

    public const VALUES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    private string $value;

    public function __construct(string $value)
    {
        self::inArray($value);

        $this->value = $value;
    }

    public static function inArray(string $value): void
    {
        Assert::inArray($value, self::VALUES);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getIndex(): int
    {
        return array_search($this->value, self::VALUES);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        return self::STATUS_ACTIVE === $this->value;
    }

    public function isInactive(): bool
    {
        return self::STATUS_INACTIVE === $this->value;
    }

    public static function getStatusChoices(): array
    {
        return [
            self::STATUS_ACTIVE => self::STATUS_ACTIVE,
            self::STATUS_INACTIVE => self::STATUS_INACTIVE,
        ];
    }
}