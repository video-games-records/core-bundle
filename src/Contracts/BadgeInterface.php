<?php
namespace VideoGamesRecords\CoreBundle\Contracts;

interface BadgeInterface
{
    const TYPE_CONNEXION = 'Connexion';
    const TYPE_DON = 'Don';
    const TYPE_FORUM = 'Forum';
    const TYPE_INSCRIPTION = 'Inscription';
    const TYPE_MASTER = 'Master';
    const TYPE_PLATFORM = 'Platform';
    const TYPE_SERIE = 'Serie';
    const TYPE_SPECIAL_WEBMASTER = 'SpecialWebmaster';
    const TYPE_VGR_CHART = 'VgrChart';
    const TYPE_VGR_PROOF = 'VgrProof';
    const TYPE_VGR_SPECIAL_COUNTRY = 'VgrSpecialCountry';
    const TYPE_VGR_SPECIAL_CUP = 'VgrSpecialCup';
    const TYPE_VGR_SPECIAL_LEGEND = 'VgrSpecialLegend';
    const TYPE_VGR_SPECIAL_MEDALS = 'VgrSpecialMedals';
    const TYPE_VGR_SPECIAL_POINTS = 'VgrSpecialPoints';
    const TYPE_TWITCH = 'Twitch';

    const DIRECTORY_DEFAULT = 'badge/';

    const DIRECTORIES = [
        self::TYPE_SERIE => 'series/badge'
    ];

    //
    const TITLE_TYPE = 'TYPE';
    const TITLE_VALUE_TYPE = 'VALUE_TYPE';
    const TITLE_TYPE_VALUE = 'TYPE_VALUE';
    const TITLE_PLATFORM = 'PLATFORM';
    const TITLE_SERIE = 'SERIE';
    const TITLE_GAME = 'GAME';
    const TITLE_COUNTRY = 'COUNTRY';


    const TITLES = [
        self::TYPE_TWITCH               => self::TITLE_TYPE,
        self::TYPE_INSCRIPTION          => self::TITLE_TYPE,
        self::TYPE_SPECIAL_WEBMASTER    => self::TITLE_TYPE,

        self::TYPE_CONNEXION            => self::TITLE_VALUE_TYPE,
        self::TYPE_DON                  => self::TITLE_VALUE_TYPE,
        self::TYPE_FORUM                => self::TITLE_VALUE_TYPE,
        self::TYPE_VGR_CHART            => self::TITLE_VALUE_TYPE,
        self::TYPE_VGR_PROOF            => self::TITLE_VALUE_TYPE,

        self::TYPE_VGR_SPECIAL_CUP      => self::TITLE_TYPE_VALUE,
        self::TYPE_VGR_SPECIAL_LEGEND   => self::TITLE_TYPE_VALUE,
        self::TYPE_VGR_SPECIAL_MEDALS   => self::TITLE_TYPE_VALUE,
        self::TYPE_VGR_SPECIAL_POINTS   => self::TITLE_TYPE_VALUE,

        self::TYPE_MASTER               => self::TITLE_GAME,
        self::TYPE_PLATFORM             => self::TITLE_PLATFORM,
        self::TYPE_SERIE                => self::TITLE_SERIE,
        self::TYPE_VGR_SPECIAL_COUNTRY  => self::TITLE_COUNTRY,
    ];
}
