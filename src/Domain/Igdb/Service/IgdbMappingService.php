<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Domain\Igdb\Service;

use VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping\PlatformMapping;
use VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping\GameMapping;
use VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping\GenreMapping;

/**
 * Central service for all IGDB mappings
 */
class IgdbMappingService
{
    public function __construct(
        private readonly GameMapping $gameMapping
    ) {
    }
    /**
     * Platform mapping methods
     */
    public function getPlatformIgdbId(int $vgrPlatformId): ?int
    {
        return PlatformMapping::getIgdbId($vgrPlatformId);
    }

    public function getPlatformVgrId(int $igdbPlatformId): ?int
    {
        return PlatformMapping::getVgrId($igdbPlatformId);
    }

    public function hasPlatformMapping(int $vgrPlatformId): bool
    {
        return PlatformMapping::hasMapping($vgrPlatformId);
    }

    /**
     * Game mapping methods (database-based)
     */
    public function getGameIgdbId(int $vgrGameId): ?int
    {
        return $this->gameMapping->getIgdbId($vgrGameId);
    }

    public function getGameVgrId(int $igdbGameId): ?int
    {
        return $this->gameMapping->getVgrId($igdbGameId);
    }

    public function hasGameMapping(int $vgrGameId): bool
    {
        return $this->gameMapping->hasMapping($vgrGameId);
    }

    public function setGameMapping(int $vgrGameId, ?int $igdbGameId): bool
    {
        return $this->gameMapping->setMapping($vgrGameId, $igdbGameId);
    }

    public function removeGameMapping(int $vgrGameId): bool
    {
        return $this->gameMapping->removeMapping($vgrGameId);
    }

    /**
     * Genre mapping methods
     */
    public function getGenreIgdbId(int $vgrGenreId): ?int
    {
        return GenreMapping::getIgdbId($vgrGenreId);
    }

    public function getGenreVgrId(int $igdbGenreId): ?int
    {
        return GenreMapping::getVgrId($igdbGenreId);
    }

    public function hasGenreMapping(int $vgrGenreId): bool
    {
        return GenreMapping::hasMapping($vgrGenreId);
    }

    /**
     * Get mapping statistics for all entity types
     */
    public function getAllMappingStats(): array
    {
        return [
            'platform' => PlatformMapping::getStats(),
            'game' => $this->gameMapping->getStats(),
            'genre' => GenreMapping::getStats(),
        ];
    }

    /**
     * Batch convert multiple entity types from VGR to IGDB
     */
    public function convertBatchVgrToIgdb(array $data): array
    {
        $result = [];

        if (isset($data['platforms'])) {
            $result['platforms'] = [];
            foreach ($data['platforms'] as $vgrId) {
                $igdbId = $this->getPlatformIgdbId($vgrId);
                if ($igdbId !== null) {
                    $result['platforms'][$vgrId] = $igdbId;
                }
            }
        }

        if (isset($data['games'])) {
            $result['games'] = [];
            foreach ($data['games'] as $vgrId) {
                $igdbId = $this->getGameIgdbId($vgrId);
                if ($igdbId !== null) {
                    $result['games'][$vgrId] = $igdbId;
                }
            }
        }

        if (isset($data['genres'])) {
            $result['genres'] = [];
            foreach ($data['genres'] as $vgrId) {
                $igdbId = $this->getGenreIgdbId($vgrId);
                if ($igdbId !== null) {
                    $result['genres'][$vgrId] = $igdbId;
                }
            }
        }

        return $result;
    }
}