<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Enum;

enum BadgeType: string
{
    case CONNEXION = 'Connexion';
    case DON = 'Don';
    case FORUM = 'Forum';
    case INSCRIPTION = 'Inscription';
    case MASTER = 'Master';
    case PLATFORM = 'Platform';
    case SERIE = 'Serie';
    case SPECIAL_WEBMASTER = 'SpecialWebmaster';
    case VGR_CHART = 'VgrChart';
    case VGR_PROOF = 'VgrProof';
    case VGR_SPECIAL_COUNTRY = 'VgrSpecialCountry';
    case VGR_SPECIAL_CUP = 'VgrSpecialCup';
    case VGR_SPECIAL_LEGEND = 'VgrSpecialLegend';
    case VGR_SPECIAL_MEDALS = 'VgrSpecialMedals';
    case VGR_SPECIAL_POINTS = 'VgrSpecialPoints';

    /**
     * Retourne tous les badges de type "Special"
     */
    public static function getSpecialBadges(): array
    {
        return [
            self::INSCRIPTION,
            self::SPECIAL_WEBMASTER,
            self::VGR_SPECIAL_COUNTRY,
            self::VGR_SPECIAL_CUP,
            self::VGR_SPECIAL_LEGEND,
            self::VGR_SPECIAL_MEDALS,
            self::VGR_SPECIAL_POINTS,
        ];
    }

    /**
     * Retourne les valeurs string des badges spéciaux pour les requêtes Doctrine
     */
    public static function getSpecialBadgeValues(): array
    {
        return array_map(fn(BadgeType $badge) => $badge->value, self::getSpecialBadges());
    }

    /**
     * Vérifie si le badge est de type "Special"
     */
    public function isSpecial(): bool
    {
        return in_array($this, self::getSpecialBadges(), true);
    }
}
