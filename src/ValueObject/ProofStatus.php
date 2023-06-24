<?php

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class ProofStatus
{
    const STATUS_IN_PROGRESS = 'IN PROGRESS';
    const STATUS_REFUSED = 'REFUSED';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_DELETED = 'DELETED';

    public const VALUES = [
        self::STATUS_IN_PROGRESS,
        self::STATUS_REFUSED,
        self::STATUS_ACCEPTED,
        self::STATUS_CLOSED,
        self::STATUS_DELETED,
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
        return self::STATUS_IN_PROGRESS === $this->value;
    }


    /**
     * @return array
     */
    public static function getStatusChoices(): array
    {
        return [
            self::STATUS_IN_PROGRESS => self::STATUS_IN_PROGRESS,
            self::STATUS_REFUSED => self::STATUS_REFUSED,
            self::STATUS_ACCEPTED => self::STATUS_ACCEPTED,
            self::STATUS_CLOSED => self::STATUS_CLOSED
        ];
    }
}
