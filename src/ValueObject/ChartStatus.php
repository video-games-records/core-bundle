<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class ChartStatus
{
    public const NORMAL = 'NORMAL';
    public const MAJ = 'MAJ';
    public const ERROR = 'ERROR';

    public const VALUES = [
        self::NORMAL => self::NORMAL,
        self::MAJ => self::MAJ,
        self::ERROR => self::ERROR,
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
        return self::NORMAL === $this->value;
    }

    public function isMaj(): bool
    {
        return self::MAJ === $this->value;
    }

    public function isError(): bool
    {
        return self::ERROR === $this->value;
    }

    public static function getStatusChoices(): array
    {
        return [
            'label.chart.status.normal' => self::NORMAL,
            'label.chart.status.maj' => self::MAJ,
            'label.chart.status.error' => self::ERROR,
        ];
    }
}
