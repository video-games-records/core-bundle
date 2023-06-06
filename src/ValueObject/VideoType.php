<?php

namespace VideoGamesRecords\CoreBundle\ValueObject;

use Webmozart\Assert\Assert;

class VideoType
{
    const TYPE_YOUTUBE = 'Youtube';
    const TYPE_TWITCH = 'Twitch';
    const TYPE_UNKNOWN = 'Unknown';

    public const VALUES = [
        self::TYPE_YOUTUBE,
        self::TYPE_TWITCH,
        self::TYPE_UNKNOWN,
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


    public static function getTypeChoices(): array
    {
        return [
            self::TYPE_YOUTUBE  => self::TYPE_YOUTUBE,
            self::TYPE_TWITCH   => self::TYPE_TWITCH,
            self::TYPE_UNKNOWN  => self::TYPE_UNKNOWN,
        ];
    }
}