<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class ProofRequestStatus
{
    public const IN_PROGRESS = 'IN PROGRESS';
    public const REFUSED = 'REFUSED';
    public const ACCEPTED = 'ACCEPTED';

    public const VALUES = [
        self::IN_PROGRESS,
        self::REFUSED,
        self::ACCEPTED
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

    public function isInProgress(): bool
    {
        return self::IN_PROGRESS === $this->value;
    }


    /**
     * @return array
     */
    public static function getStatusChoices(): array
    {
        return [
            self::IN_PROGRESS => self::IN_PROGRESS,
            self::REFUSED => self::REFUSED,
            self::ACCEPTED => self::ACCEPTED
        ];
    }
}
