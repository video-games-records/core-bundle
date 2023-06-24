<?php

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class ChartStatus
{
    const STATUS_NORMAL = 'NORMAL';
    const STATUS_MAJ = 'MAJ';
    const STATUS_ERROR = 'ERROR';

    public const VALUES = [
        self::STATUS_NORMAL => self::STATUS_NORMAL,
        self::STATUS_MAJ => self::STATUS_MAJ,
        self::STATUS_ERROR => self::STATUS_ERROR,
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

    public function isNormal(): bool
    {
        return self::STATUS_NORMAL === $this->value;
    }

    public function isMaj(): bool
    {
        return self::STATUS_MAJ === $this->value;
    }

    public function isError(): bool
    {
        return self::STATUS_ERROR === $this->value;
    }

    public static function getStatusChoices(): array
    {
        return [
            'label.chart.status.normal' => self::STATUS_NORMAL,
            'label.chart.status.maj' => self::STATUS_MAJ,
            'label.chart.status.error' => self::STATUS_ERROR,
        ];
    }
}
