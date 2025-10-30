<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Repository\VideoRepository;
use VideoGamesRecords\CoreBundle\Service\VideoRelevanceScorer;

class VideoRecommendationService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'video_recommendations_';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CacheItemPoolInterface $cache,
        private readonly LoggerInterface $logger,
        private readonly VideoRelevanceScorer $relevanceScorer
    ) {
    }

    public function getRelatedVideos(Video $video, int $limit = 10): array
    {
        $cacheKey = self::CACHE_PREFIX . $video->getId();
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            $this->logger->info('Video recommendations served from cache', [
                'videoId' => $video->getId(),
                'cacheKey' => $cacheKey
            ]);
            return $cacheItem->get();
        }

        $recommendations = $this->generateRecommendations($video, $limit);

        $cacheItem->set($recommendations);
        $cacheItem->expiresAfter(self::CACHE_TTL);
        $this->cache->save($cacheItem);

        $this->logger->info('Video recommendations generated and cached', [
            'videoId' => $video->getId(),
            'recommendationsCount' => count($recommendations),
            'cacheKey' => $cacheKey,
            'ttl' => self::CACHE_TTL
        ]);

        return $recommendations;
    }

    private function generateRecommendations(Video $video, int $limit): array
    {
        $recommendations = [];
        $usedVideoIds = [$video->getId()];

        // Strategy 1: Same game videos (25% - 2-3 videos)
        $sameGameCount = max(1, (int) ceil($limit * 0.25));
        $sameGameVideos = $this->getVideosBySameGame($video, $sameGameCount, $usedVideoIds);
        $recommendations = array_merge($recommendations, $sameGameVideos);
        $usedVideoIds = array_merge($usedVideoIds, $this->extractVideoIds($sameGameVideos));

        // Strategy 2: Same series videos (25% - 2-3 videos)
        $sameSeriesCount = max(1, (int) ceil($limit * 0.25));
        $sameSeriesVideos = $this->getVideosBySameSeries($video, $sameSeriesCount, $usedVideoIds);
        $recommendations = array_merge($recommendations, $sameSeriesVideos);
        $usedVideoIds = array_merge($usedVideoIds, $this->extractVideoIds($sameSeriesVideos));

        // Strategy 3: Same genre videos via IGDB (40% - 3-4 videos)
        $sameGenreCount = max(1, (int) ceil($limit * 0.40));
        $sameGenreVideos = $this->getVideosBySameGenres($video, $sameGenreCount, $usedVideoIds);
        $recommendations = array_merge($recommendations, $sameGenreVideos);
        $usedVideoIds = array_merge($usedVideoIds, $this->extractVideoIds($sameGenreVideos));

        // Strategy 4: Popular/Random videos to fill remaining slots (10%)
        $remainingCount = $limit - count($recommendations);
        if ($remainingCount > 0) {
            $randomVideos = $this->getPopularRandomVideos($remainingCount, $usedVideoIds);
            $recommendations = array_merge($recommendations, $randomVideos);
        }

        // Nouvelle approche : utiliser le scoring de pertinence au lieu du shuffle
        return $this->applyRelevanceScoring($video, $recommendations, $limit);
    }

    private function getVideosBySameGame(Video $video, int $limit, array $excludeIds): array
    {
        if (!$video->getGame()) {
            return [];
        }

        /** @var VideoRepository $repository */
        $repository = $this->entityManager->getRepository(Video::class);

        $qb = $repository->createQueryBuilder('v')
            ->where('v.game = :game')
            ->andWhere('v.id NOT IN (:excludeIds)')
            ->andWhere('v.isActive = true')
            ->setParameter('game', $video->getGame())
            ->setParameter('excludeIds', $excludeIds)
            ->orderBy('v.viewCount', 'DESC')
            ->addOrderBy('v.createdAt', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    private function getVideosBySameSeries(Video $video, int $limit, array $excludeIds): array
    {
        if (!$video->getGame()?->getSerie()) {
            return [];
        }

        /** @var VideoRepository $repository */
        $repository = $this->entityManager->getRepository(Video::class);

        $qb = $repository->createQueryBuilder('v')
            ->join('v.game', 'g')
            ->where('g.serie = :serie')
            ->andWhere('v.id NOT IN (:excludeIds)')
            ->andWhere('v.isActive = true')
            ->setParameter('serie', $video->getGame()->getSerie())
            ->setParameter('excludeIds', $excludeIds)
            ->orderBy('v.viewCount', 'DESC')
            ->addOrderBy('v.createdAt', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    private function getVideosBySameGenres(Video $video, int $limit, array $excludeIds): array
    {
        $game = $video->getGame();
        if (!$game?->getIgdbGame()) {
            return [];
        }

        $genres = $game->getIgdbGame()->getGenres();
        if ($genres->isEmpty()) {
            return [];
        }

        $genreIds = [];
        foreach ($genres as $genre) {
            $genreIds[] = $genre->getId();
        }

        /** @var VideoRepository $repository */
        $repository = $this->entityManager->getRepository(Video::class);

        $qb = $repository->createQueryBuilder('v')
            ->join('v.game', 'g')
            ->join('g.igdbGame', 'ig')
            ->join('ig.genres', 'genre')
            ->where('genre.id IN (:genreIds)')
            ->andWhere('v.id NOT IN (:excludeIds)')
            ->andWhere('v.isActive = true')
            ->andWhere('g.id != :currentGameId')
            ->setParameter('genreIds', $genreIds)
            ->setParameter('excludeIds', $excludeIds)
            ->setParameter('currentGameId', $game->getId())
            ->groupBy('v.id')
            ->orderBy('COUNT(genre.id)', 'DESC') // Games with more matching genres first
            ->addOrderBy('v.viewCount', 'DESC')
            ->addOrderBy('v.createdAt', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    private function getPopularRandomVideos(int $limit, array $excludeIds): array
    {
        /** @var VideoRepository $repository */
        $repository = $this->entityManager->getRepository(Video::class);

        // Get popular videos from the last 30 days
        $qb = $repository->createQueryBuilder('v')
            ->where('v.id NOT IN (:excludeIds)')
            ->andWhere('v.isActive = true')
            ->andWhere('v.createdAt >= :thirtyDaysAgo')
            ->setParameter('excludeIds', $excludeIds)
            ->setParameter('thirtyDaysAgo', new \DateTime('-30 days'))
            ->orderBy('v.viewCount', 'DESC')
            ->addOrderBy('v.likeCount', 'DESC')
            ->setMaxResults($limit * 3); // Get more to randomize

        $videos = $qb->getQuery()->getResult();

        // Randomize and limit
        shuffle($videos);
        $videos = array_slice($videos, 0, $limit);

        return $videos;
    }

    private function extractVideoIds(array $videos): array
    {
        return array_map(fn(Video $video) => $video->getId(), $videos);
    }

    private function applyRelevanceScoring(Video $sourceVideo, array $candidateVideos, int $limit): array
    {
        // Utiliser le scorer pour trier par pertinence
        $rankedVideos = $this->relevanceScorer->rankVideos($sourceVideo, $candidateVideos);

        // Appliquer la diversification pour éviter la surreprésentation
        return $this->diversifyRecommendations($rankedVideos, $limit);
    }

    /**
     * Diversifie les recommandations pour éviter trop de vidéos du même jeu/joueur
     */
    private function diversifyRecommendations(array $rankedVideos, int $limit): array
    {
        $result = [];
        $gameCount = [];
        $playerCount = [];
        $seriesCount = [];

        // Règles de diversification (configurables)
        $maxPerGame = max(1, (int) ceil($limit * 0.4)); // Max 40% du même jeu
        $maxPerPlayer = max(1, (int) ceil($limit * 0.3)); // Max 30% du même joueur
        $maxPerSeries = max(1, (int) ceil($limit * 0.6)); // Max 60% de la même série

        foreach ($rankedVideos as $video) {
            if (count($result) >= $limit) {
                break;
            }

            $gameId = $video->getGame()?->getId() ?? 'no_game';
            $playerId = $video->getPlayer()?->getId() ?? 'no_player';
            $seriesId = $video->getGame()?->getSerie()?->getId() ?? 'no_series';

            $currentGameCount = $gameCount[$gameId] ?? 0;
            $currentPlayerCount = $playerCount[$playerId] ?? 0;
            $currentSeriesCount = $seriesCount[$seriesId] ?? 0;

            // Vérifier les limites de diversification
            if (
                $currentGameCount < $maxPerGame &&
                $currentPlayerCount < $maxPerPlayer &&
                $currentSeriesCount < $maxPerSeries
            ) {
                $result[] = $video;
                $gameCount[$gameId] = $currentGameCount + 1;
                $playerCount[$playerId] = $currentPlayerCount + 1;
                $seriesCount[$seriesId] = $currentSeriesCount + 1;
            }
        }

        // Si on n'a pas assez de vidéos à cause des contraintes,
        // ajouter les meilleures restantes sans contraintes
        if (count($result) < $limit) {
            $remaining = array_diff($rankedVideos, $result);
            $needed = $limit - count($result);
            $result = array_merge($result, array_slice($remaining, 0, $needed));
        }

        return $result;
    }

    /**
     * Méthode pour débugger les scores de recommandation
     */
    public function getRecommendationsWithScores(Video $video, int $limit = 10): array
    {
        $recommendations = [];
        $usedVideoIds = [$video->getId()];

        // Récupérer les candidats de la même façon
        $sameGameCount = max(1, (int) ceil($limit * 0.25));
        $sameGameVideos = $this->getVideosBySameGame($video, $sameGameCount, $usedVideoIds);
        $recommendations = array_merge($recommendations, $sameGameVideos);
        $usedVideoIds = array_merge($usedVideoIds, $this->extractVideoIds($sameGameVideos));

        $sameSeriesCount = max(1, (int) ceil($limit * 0.25));
        $sameSeriesVideos = $this->getVideosBySameSeries($video, $sameSeriesCount, $usedVideoIds);
        $recommendations = array_merge($recommendations, $sameSeriesVideos);
        $usedVideoIds = array_merge($usedVideoIds, $this->extractVideoIds($sameSeriesVideos));

        $sameGenreCount = max(1, (int) ceil($limit * 0.40));
        $sameGenreVideos = $this->getVideosBySameGenres($video, $sameGenreCount, $usedVideoIds);
        $recommendations = array_merge($recommendations, $sameGenreVideos);
        $usedVideoIds = array_merge($usedVideoIds, $this->extractVideoIds($sameGenreVideos));

        $remainingCount = $limit - count($recommendations);
        if ($remainingCount > 0) {
            $randomVideos = $this->getPopularRandomVideos($remainingCount, $usedVideoIds);
            $recommendations = array_merge($recommendations, $randomVideos);
        }

        // Retourner avec les scores pour debug
        return $this->relevanceScorer->rankVideosWithScores($video, $recommendations);
    }

    public function clearVideoRecommendationsCache(Video $video): bool
    {
        $cacheKey = self::CACHE_PREFIX . $video->getId();
        return $this->cache->deleteItem($cacheKey);
    }

    public function getRecommendationStats(Video $video): array
    {
        $game = $video->getGame();
        $stats = [
            'video_id' => $video->getId(),
            'has_game' => $game !== null,
            'has_series' => $game?->getSerie() !== null,
            'has_igdb_game' => $game?->getIgdbGame() !== null,
            'genres_count' => 0,
            'cache_status' => 'miss'
        ];

        if ($game?->getIgdbGame()) {
            $stats['genres_count'] = $game->getIgdbGame()->getGenres()->count();
        }

        $cacheKey = self::CACHE_PREFIX . $video->getId();
        $cacheItem = $this->cache->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            $stats['cache_status'] = 'hit';
        }

        return $stats;
    }
}
