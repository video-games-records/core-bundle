<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping;

use VideoGamesRecords\CoreBundle\Repository\GameRepository;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\IgdbBundle\Entity\Game as IgdbGame;

/**
 * Database-based mapping between VGR Games and IGDB Games
 * Uses the ManyToOne relation in the Game entity to store mappings
 */
class GameMapping
{
    public function __construct(
        private readonly GameRepository $gameRepository
    ) {
    }

    /**
     * Get IGDB Game entity from VGR Game ID
     */
    public function getIgdbGame(int $vgrGameId): ?IgdbGame
    {
        return $this->gameRepository->findIgdbGame($vgrGameId);
    }

    /**
     * Get IGDB Game ID from VGR Game ID
     */
    public function getIgdbId(int $vgrGameId): ?int
    {
        return $this->gameRepository->findIgdbId($vgrGameId);
    }

    /**
     * Get VGR Game entity from IGDB Game entity
     */
    public function getVgrGame(IgdbGame $igdbGame): ?Game
    {
        return $this->gameRepository->findVgrGameByIgdbGame($igdbGame);
    }

    /**
     * Get VGR Game ID from IGDB Game ID
     */
    public function getVgrId(int $igdbGameId): ?int
    {
        return $this->gameRepository->findVgrIdByIgdbId($igdbGameId);
    }

    /**
     * Check if VGR Game has IGDB mapping
     */
    public function hasMapping(int $vgrGameId): bool
    {
        return $this->getIgdbId($vgrGameId) !== null;
    }

    /**
     * Get all VGR Game IDs that have IGDB mappings
     */
    public function getMappedVgrIds(): array
    {
        return $this->gameRepository->findVgrIdsWithIgdbMapping();
    }

    /**
     * Get all IGDB Game IDs that are mapped from VGR
     */
    public function getMappedIgdbIds(): array
    {
        return $this->gameRepository->findMappedIgdbIds();
    }

    /**
     * Set IGDB Game entity for a VGR Game
     */
    public function setMappingEntity(int $vgrGameId, ?IgdbGame $igdbGame): bool
    {
        return $this->gameRepository->updateIgdbGame($vgrGameId, $igdbGame);
    }

    /**
     * Set IGDB ID for a VGR Game (compatibility method)
     */
    public function setMapping(int $vgrGameId, ?int $igdbGameId): bool
    {
        return $this->gameRepository->updateIgdbId($vgrGameId, $igdbGameId);
    }

    /**
     * Remove IGDB mapping for a VGR Game
     */
    public function removeMapping(int $vgrGameId): bool
    {
        return $this->setMapping($vgrGameId, null);
    }

    /**
     * Get mapping statistics
     */
    public function getStats(): array
    {
        return $this->gameRepository->getIgdbMappingStats();
    }

    /**
     * Batch update mappings
     */
    public function batchUpdateMappings(array $mappings): int
    {
        return $this->gameRepository->batchUpdateIgdbMappings($mappings);
    }
}