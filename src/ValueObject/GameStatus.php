<?php

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class GameStatus
{
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_CREATED = 'CREATED';
    const STATUS_ADD_PICTURE = 'ADD_PICTURE';
    const STATUS_ADD_SCORE = 'ADD_SCORE';
    const STATUS_COMPLETED = 'COMPLETED';

    public const VALUES = [
        self::STATUS_CREATED,
        self::STATUS_ADD_SCORE,
        self::STATUS_ADD_PICTURE,
        self::STATUS_COMPLETED,
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
            self::STATUS_CREATED . ' (1)' => self::STATUS_CREATED,
            self::STATUS_ADD_SCORE . ' (2)' => self::STATUS_ADD_SCORE,
            self::STATUS_ADD_PICTURE . ' (3)' => self::STATUS_ADD_PICTURE,
            self::STATUS_COMPLETED . ' (4)' => self::STATUS_COMPLETED,
            self::STATUS_ACTIVE . ' (5)' => self::STATUS_ACTIVE,
            self::STATUS_INACTIVE => self::STATUS_INACTIVE,
        ];
    }

    public static function getReverseStatusChoices(): array
    {
        return [
            self::STATUS_CREATED => self::STATUS_CREATED . ' (1)',
            self::STATUS_ADD_SCORE => self::STATUS_ADD_SCORE . ' (2)',
            self::STATUS_ADD_PICTURE => self::STATUS_ADD_PICTURE . ' (3)',
            self::STATUS_COMPLETED => self::STATUS_COMPLETED . ' (4)',
            self::STATUS_ACTIVE => self::STATUS_ACTIVE . ' (5)',
            self::STATUS_INACTIVE => self::STATUS_INACTIVE,
        ];
    }
}