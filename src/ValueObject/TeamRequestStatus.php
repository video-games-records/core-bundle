<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class TeamRequestStatus
{
    public const ACTIVE = 'ACTIVE';
    public const ACCEPTED = 'ACCEPTED';
    public const CANCELED = 'CANCELED';
    public const REFUSED = 'REFUSED';


    public const VALUES = [
        self::ACTIVE,
        self::ACCEPTED,
        self::CANCELED,
        self::REFUSED,
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

    public function isAccepted(): bool
    {
        return self::ACCEPTED === $this->value;
    }

    public function isRefused(): bool
    {
        return self::REFUSED === $this->value;
    }

    public function isCanceled(): bool
    {
        return self::CANCELED === $this->value;
    }

    /**
     * @return array
     */
    public static function getStatusChoices(): array
    {
        return [
            self::ACTIVE => self::ACTIVE,
            self::ACCEPTED => self::ACCEPTED,
            self::REFUSED => self::REFUSED,
            self::CANCELED => self::CANCELED,
        ];
    }
}
