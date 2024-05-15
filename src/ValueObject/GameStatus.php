<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class GameStatus
{
    public const ACTIVE = 'ACTIVE';
    public const INACTIVE = 'INACTIVE';
    public const CREATED = 'CREATED';
    public const ADD_PICTURE = 'ADD_PICTURE';
    public const ADD_SCORE = 'ADD_SCORE';
    public const COMPLETED = 'COMPLETED';

    public const VALUES = [
        self::CREATED,
        self::ADD_SCORE,
        self::ADD_PICTURE,
        self::COMPLETED,
        self::ACTIVE,
        self::INACTIVE,
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

    public function __toString(): string
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        return self::ACTIVE === $this->value;
    }

    public function isInactive(): bool
    {
        return self::INACTIVE === $this->value;
    }

    public static function getStatusChoices(): array
    {
        return [
            self::CREATED . ' (1)' => self::CREATED,
            self::ADD_SCORE . ' (2)' => self::ADD_SCORE,
            self::ADD_PICTURE . ' (3)' => self::ADD_PICTURE,
            self::COMPLETED . ' (4)' => self::COMPLETED,
            self::ACTIVE . ' (5)' => self::ACTIVE,
            self::INACTIVE => self::INACTIVE,
        ];
    }

    public static function getReverseStatusChoices(): array
    {
        return [
            self::CREATED => self::CREATED . ' (1)',
            self::ADD_SCORE => self::ADD_SCORE . ' (2)',
            self::ADD_PICTURE => self::ADD_PICTURE . ' (3)',
            self::COMPLETED => self::COMPLETED . ' (4)',
            self::ACTIVE => self::ACTIVE . ' (5)',
            self::INACTIVE => self::INACTIVE,
        ];
    }
}
