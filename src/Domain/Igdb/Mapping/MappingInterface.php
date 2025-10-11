<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping;

/**
 * Interface for IGDB mappings
 */
interface MappingInterface
{
    /**
     * Get IGDB ID from VGR ID
     */
    public static function getIgdbId(int $vgrId): ?int;

    /**
     * Get VGR ID from IGDB ID
     */
    public static function getVgrId(int $igdbId): ?int;

    /**
     * Check if VGR ID has IGDB mapping
     */
    public static function hasMapping(int $vgrId): bool;

    /**
     * Get all VGR IDs with mappings
     */
    public static function getMappedVgrIds(): array;

    /**
     * Get all IGDB IDs that are mapped
     */
    public static function getMappedIgdbIds(): array;

    /**
     * Get the complete mapping array
     */
    public static function getMapping(): array;
}
