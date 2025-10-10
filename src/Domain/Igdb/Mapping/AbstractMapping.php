<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping;

/**
 * Abstract base class for IGDB mappings
 */
abstract class AbstractMapping implements MappingInterface
{
    /**
     * Get VGR ID from IGDB ID
     */
    public static function getVgrId(int $igdbId): ?int
    {
        $mapping = array_flip(array_filter(static::getMapping()));
        return $mapping[$igdbId] ?? null;
    }

    /**
     * Check if VGR ID has IGDB mapping
     */
    public static function hasMapping(int $vgrId): bool
    {
        $mapping = static::getMapping();
        return isset($mapping[$vgrId]) && $mapping[$vgrId] !== null;
    }

    /**
     * Get all VGR IDs with mappings
     */
    public static function getMappedVgrIds(): array
    {
        return array_keys(array_filter(static::getMapping()));
    }

    /**
     * Get all IGDB IDs that are mapped
     */
    public static function getMappedIgdbIds(): array
    {
        return array_values(array_filter(static::getMapping()));
    }

    /**
     * Get VGR IDs without IGDB mapping
     */
    public static function getUnmappedVgrIds(): array
    {
        return array_keys(array_filter(static::getMapping(), fn($igdbId) => $igdbId === null));
    }

    /**
     * Get mapping statistics
     */
    public static function getStats(): array
    {
        $mapping = static::getMapping();
        $mapped = array_filter($mapping);

        return [
            'total' => count($mapping),
            'mapped' => count($mapped),
            'unmapped' => count($mapping) - count($mapped),
            'coverage' => count($mapping) > 0 ? round((count($mapped) / count($mapping)) * 100, 2) : 0
        ];
    }
}
