<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping;

/**
 * Static mapping between VGR Genre IDs and IGDB Genre IDs
 * TODO: Implement when genre mapping data is available
 */
class GenreMapping extends AbstractMapping
{
    /**
     * Mapping from VGR Genre ID to IGDB Genre ID
     * TODO: Add actual mapping data based on IGDB genre structure
     */
    private const VGR_TO_IGDB_MAPPING = [
        // Example mappings based on common genres:
        // 1 => 31, // Action => Action
        // 2 => 33, // Arcade => Arcade
        // 3 => 25, // Adventure => Adventure
        // 4 => 5,  // Shooter => Shooter
        // 5 => 10, // Racing => Racing
        // 6 => 15, // Strategy => Strategy
        // 7 => 12, // Role-playing (RPG) => Role-playing (RPG)
        // 8 => 14, // Sport => Sport
        // 9 => 2,  // Point-and-click => Point-and-click
        // 10 => 16, // Turn-based strategy (TBS) => Turn-based strategy (TBS)
    ];

    /**
     * Get IGDB Genre ID from VGR Genre ID
     */
    public static function getIgdbId(int $vgrGenreId): ?int
    {
        return self::VGR_TO_IGDB_MAPPING[$vgrGenreId] ?? null;
    }

    /**
     * Get the complete mapping array
     */
    public static function getMapping(): array
    {
        return self::VGR_TO_IGDB_MAPPING;
    }
}