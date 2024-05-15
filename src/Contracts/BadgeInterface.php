<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Contracts;

interface BadgeInterface
{
    public const TYPE_CONNEXION = 'Connexion';
    public const TYPE_DON = 'Don';
    public const TYPE_FORUM = 'Forum';
    public const TYPE_INSCRIPTION = 'Inscription';
    public const TYPE_MASTER = 'Master';
    public const TYPE_PLATFORM = 'Platform';
    public const TYPE_SERIE = 'Serie';
    public const TYPE_SPECIAL_WEBMASTER = 'SpecialWebmaster';
    public const TYPE_VGR_CHART = 'VgrChart';
    public const TYPE_VGR_PROOF = 'VgrProof';
    public const TYPE_VGR_SPECIAL_COUNTRY = 'VgrSpecialCountry';
    public const TYPE_VGR_SPECIAL_CUP = 'VgrSpecialCup';
    public const TYPE_VGR_SPECIAL_LEGEND = 'VgrSpecialLegend';
    public const TYPE_VGR_SPECIAL_MEDALS = 'VgrSpecialMedals';
    public const TYPE_VGR_SPECIAL_POINTS = 'VgrSpecialPoints';
    public const TYPE_TWITCH = 'Twitch';
    public const DIRECTORY_DEFAULT = 'badge';
    public const DIRECTORIES = [
        self::TYPE_SERIE => 'series/badge'
    ];
}
