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
    case TWITCH = 'Twitch';

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

    /**
     * Retourne le répertoire par défaut pour les badges
     */
    public static function getDefaultDirectory(): string
    {
        return 'badge';
    }

    /**
     * Retourne les répertoires spécifiques pour certains types de badges
     */
    public static function getDirectories(): array
    {
        return [
            self::SERIE->value => 'series/badge'
        ];
    }

    /**
     * Retourne le répertoire pour un type de badge spécifique
     */
    public function getDirectory(): string
    {
        $directories = self::getDirectories();
        if (array_key_exists($this->value, $directories)) {
            return $directories[$this->value];
        }
        return self::getDefaultDirectory() . DIRECTORY_SEPARATOR . $this->value;
    }
}
