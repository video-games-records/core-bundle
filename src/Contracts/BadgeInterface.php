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

    const DIRECTORY_DEFAULT = 'badge';

    const DIRECTORIES = [
        self::TYPE_SERIE => 'series/badge'
    ];
}
