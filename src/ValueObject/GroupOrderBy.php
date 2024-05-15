<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class GroupOrderBy
{
    public const NAME = 'NAME';
    public const ID = 'ID';
    public const CUSTOM = 'CUSTOM';

    public const VALUES = [
        self::NAME,
        self::ID,
        self::CUSTOM,
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

    public static function getStatusChoices(): array
    {
        return [
            self::NAME => self::NAME,
            self::ID => self::ID,
            self::CUSTOM => self::CUSTOM,
        ];
    }
}
