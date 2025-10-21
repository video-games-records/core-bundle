<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Domain\Igdb\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Domain\Igdb\DataTransformer\PlatformMappingTransformer;
use VideoGamesRecords\IgdbBundle\Client\IgdbClient;
use VideoGamesRecords\IgdbBundle\Entity\Game as IgdbGame;

class GameMatchingService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly IgdbClient $igdbClient,
        private readonly PlatformMappingTransformer $platformMappingTransformer,
        private readonly IgdbMappingService $igdbMappingService,
        private readonly LoggerInterface $logger
    ) {
    }

    public function findAndAssociateIgdbGame(Game $coreGame): ?IgdbGame
    {
        if ($coreGame->getIgdbGame() !== null) {
            $this->logger->info('Game already has IGDB association', ['gameId' => $coreGame->getId()]);
            return $coreGame->getIgdbGame();
        }

        $gameName = $coreGame->getLibGameEn();
        $this->logger->info('Searching IGDB game for: ' . $gameName, ['gameId' => $coreGame->getId()]);

        $corePlatforms = $coreGame->getPlatforms();
        $igdbPlatformIds = [];

        foreach ($corePlatforms as $corePlatform) {
            $igdbPlatformId = $this->igdbMappingService->getPlatformIgdbId($corePlatform->getId());
            if ($igdbPlatformId !== null) {
                $igdbPlatformIds[] = $igdbPlatformId;
            }
        }

        if (empty($igdbPlatformIds)) {
            $this->logger->warning('No IGDB platform mappings found for game', [
                'gameId' => $coreGame->getId(),
                'gameName' => $gameName
            ]);
            return null;
        }

        $this->logger->info('Using IGDB platform IDs for search', [
            'gameId' => $coreGame->getId(),
            'igdbPlatformIds' => $igdbPlatformIds
        ]);

        $igdbGames = $this->igdbClient->searchGamesByName($gameName, $igdbPlatformIds, 50);

        foreach ($igdbGames as $igdbGameData) {
            if ($this->isExactMatch($coreGame, $igdbGameData)) {
                $igdbGame = $this->findOrCreateIgdbGame($igdbGameData);

                if ($igdbGame !== null) {
                    $coreGame->setIgdbGame($igdbGame);
                    $this->igdbMappingService->setGameMapping($coreGame->getId(), $igdbGame->getId());

                    $this->entityManager->persist($coreGame);
                    $this->entityManager->flush();

                    $this->logger->info('Successfully associated IGDB game', [
                        'coreGameId' => $coreGame->getId(),
                        'igdbGameId' => $igdbGame->getId(),
                        'gameName' => $gameName
                    ]);

                    return $igdbGame;
                }
            }
        }

        $this->logger->info('No exact match found in IGDB', [
            'gameId' => $coreGame->getId(),
            'gameName' => $gameName,
            'searchResults' => count($igdbGames)
        ]);

        return null;
    }

    private function isExactMatch(Game $coreGame, array $igdbGameData): bool
    {
        $coreGameName = strtolower(trim($coreGame->getLibGameEn()));
        $igdbGameName = strtolower(trim($igdbGameData['name']));

        if ($coreGameName !== $igdbGameName) {
            return false;
        }

        $corePlatformIds = [];
        foreach ($coreGame->getPlatforms() as $platform) {
            $igdbPlatformId = $this->igdbMappingService->getPlatformIgdbId($platform->getId());
            if ($igdbPlatformId !== null) {
                $corePlatformIds[] = $igdbPlatformId;
            }
        }

        $igdbPlatformIds = [];
        if (isset($igdbGameData['platforms']) && is_array($igdbGameData['platforms'])) {
            foreach ($igdbGameData['platforms'] as $platformData) {
                if (is_array($platformData) && isset($platformData['id'])) {
                    $igdbPlatformIds[] = $platformData['id'];
                } elseif (is_numeric($platformData)) {
                    $igdbPlatformIds[] = (int) $platformData;
                } elseif (is_object($platformData) && property_exists($platformData, 'id')) {
                    $igdbPlatformIds[] = $platformData->id;
                }
            }
        }

        $commonPlatforms = array_intersect($corePlatformIds, $igdbPlatformIds);

        return !empty($commonPlatforms);
    }

    private function findOrCreateIgdbGame(array $igdbGameData): ?IgdbGame
    {
        $igdbGameRepository = $this->entityManager->getRepository(IgdbGame::class);
        $existingGame = $igdbGameRepository->find($igdbGameData['id']);

        if ($existingGame) {
            return $existingGame;
        }

        try {
            $igdbGame = new IgdbGame();
            $igdbGame->setId($igdbGameData['id']);
            $igdbGame->setName($igdbGameData['name']);
            $igdbGame->setSlug($igdbGameData['slug'] ?? null);
            $igdbGame->setStoryline($igdbGameData['storyline'] ?? null);
            $igdbGame->setSummary($igdbGameData['summary'] ?? null);
            $igdbGame->setUrl($igdbGameData['url'] ?? null);
            $igdbGame->setChecksum($igdbGameData['checksum'] ?? null);
            $igdbGame->setFirstReleaseDate($igdbGameData['first_release_date'] ?? null);

            if (isset($igdbGameData['created_at'])) {
                $igdbGame->setCreatedAt(new \DateTimeImmutable('@' . $igdbGameData['created_at']));
            }

            if (isset($igdbGameData['updated_at'])) {
                $igdbGame->setUpdatedAt(new \DateTimeImmutable('@' . $igdbGameData['updated_at']));
            }

            $this->entityManager->persist($igdbGame);
            $this->entityManager->flush();

            return $igdbGame;
        } catch (\Exception $e) {
            $this->logger->error('Failed to create IGDB game', [
                'igdbGameId' => $igdbGameData['id'],
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    public function getMatchingStatistics(): array
    {
        $gameRepository = $this->entityManager->getRepository(Game::class);

        $totalGames = $gameRepository->createQueryBuilder('g')
            ->select('COUNT(g.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $gamesWithIgdbAssociation = $gameRepository->createQueryBuilder('g')
            ->select('COUNT(g.id)')
            ->where('g.igdbGame IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        $gamesWithoutIgdbAssociation = $totalGames - $gamesWithIgdbAssociation;

        return [
            'total_games' => $totalGames,
            'games_with_igdb_association' => $gamesWithIgdbAssociation,
            'games_without_igdb_association' => $gamesWithoutIgdbAssociation,
            'matching_percentage' => $totalGames > 0 ? round(($gamesWithIgdbAssociation / $totalGames) * 100, 2) : 0
        ];
    }
}
