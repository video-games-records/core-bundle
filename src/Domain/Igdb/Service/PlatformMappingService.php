<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Domain\Igdb\Service;

use VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping\PlatformMapping;

/**
 * Service to handle mapping between VGR Platform IDs and IGDB Platform IDs
 */
class PlatformMappingService
{
    /**
     * Get IGDB Platform ID from VGR Platform ID
     */
    public function getIgdbPlatformId(int $vgrPlatformId): ?int
    {
        return PlatformMapping::getIgdbId($vgrPlatformId);
    }

    /**
     * Get VGR Platform ID from IGDB Platform ID
     */
    public function getVgrPlatformId(int $igdbPlatformId): ?int
    {
        return PlatformMapping::getVgrId($igdbPlatformId);
    }

    /**
     * Check if a VGR Platform has an IGDB mapping
     */
    public function hasIgdbMapping(int $vgrPlatformId): bool
    {
        return PlatformMapping::hasMapping($vgrPlatformId);
    }

    /**
     * Get all VGR Platform IDs that have IGDB mappings
     */
    public function getVgrPlatformsWithIgdbMapping(): array
    {
        return PlatformMapping::getMappedVgrIds();
    }

    /**
     * Get all IGDB Platform IDs that are mapped from VGR
     */
    public function getMappedIgdbPlatformIds(): array
    {
        return PlatformMapping::getMappedIgdbIds();
    }

    /**
     * Get the complete mapping array (for debugging or bulk operations)
     */
    public function getCompleteMapping(): array
    {
        return PlatformMapping::getMapping();
    }

    /**
     * Batch convert VGR Platform IDs to IGDB Platform IDs
     */
    public function convertVgrToIgdbBatch(array $vgrPlatformIds): array
    {
        $result = [];
        foreach ($vgrPlatformIds as $vgrId) {
            $igdbId = $this->getIgdbPlatformId($vgrId);
            if ($igdbId !== null) {
                $result[$vgrId] = $igdbId;
            }
        }
        return $result;
    }

    /**
     * Batch convert IGDB Platform IDs to VGR Platform IDs
     */
    public function convertIgdbToVgrBatch(array $igdbPlatformIds): array
    {
        $result = [];
        foreach ($igdbPlatformIds as $igdbId) {
            $vgrId = $this->getVgrPlatformId($igdbId);
            if ($vgrId !== null) {
                $result[$igdbId] = $vgrId;
            }
        }
        return $result;
    }
}