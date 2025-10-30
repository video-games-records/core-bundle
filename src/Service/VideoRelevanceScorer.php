<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Service;

use VideoGamesRecords\CoreBundle\Entity\Video;

class VideoRelevanceScorer
{
    // Poids des différents critères
    private const WEIGHTS = [
        'same_game' => 100,
        'same_series' => 60,
        'genre_match' => 15,
        'platform_match' => 10,
        'popularity_recent' => 25,
        'popularity_all_time' => 10,
        'recency_bonus' => 5,
    ];

    public function calculateScore(Video $sourceVideo, Video $candidateVideo): float
    {
        $score = 0;

        // 1. Même jeu = score maximum
        if ($this->isSameGame($sourceVideo, $candidateVideo)) {
            $score += self::WEIGHTS['same_game'];
        }

        // 2. Même série = score élevé (même si jeu différent)
        if ($this->isSameSeries($sourceVideo, $candidateVideo)) {
            $score += self::WEIGHTS['same_series'];
        }

        // 3. Genres communs = score proportionnel
        $commonGenres = $this->getCommonGenresCount($sourceVideo, $candidateVideo);
        $score += $commonGenres * self::WEIGHTS['genre_match'];

        // 4. Plateformes communes = bonus
        $commonPlatforms = $this->getCommonPlatformsCount($sourceVideo, $candidateVideo);
        $score += $commonPlatforms * self::WEIGHTS['platform_match'];

        // 5. Bonus popularité récente (30 derniers jours)
        if ($this->isRecentlyPopular($candidateVideo)) {
            $score += self::WEIGHTS['popularity_recent'];
        }

        // 6. Bonus popularité générale
        $popularityScore = $this->getPopularityScore($candidateVideo);
        $score += $popularityScore * self::WEIGHTS['popularity_all_time'];

        // 7. Bonus fraîcheur (vidéos récentes)
        $recencyScore = $this->getRecencyScore($candidateVideo);
        $score += $recencyScore * self::WEIGHTS['recency_bonus'];

        return $score;
    }

    private function isSameGame(Video $source, Video $candidate): bool
    {
        $sourceGame = $source->getGame();
        $candidateGame = $candidate->getGame();

        return $sourceGame && $candidateGame &&
               $sourceGame->getId() === $candidateGame->getId();
    }

    private function isSameSeries(Video $source, Video $candidate): bool
    {
        $sourceSeries = $source->getGame()?->getSerie();
        $candidateSeries = $candidate->getGame()?->getSerie();

        return $sourceSeries && $candidateSeries &&
               $sourceSeries->getId() === $candidateSeries->getId();
    }

    private function getCommonGenresCount(Video $source, Video $candidate): int
    {
        $sourceGenres = $this->getGenreIds($source);
        $candidateGenres = $this->getGenreIds($candidate);

        return count(array_intersect($sourceGenres, $candidateGenres));
    }

    private function getCommonPlatformsCount(Video $source, Video $candidate): int
    {
        $sourcePlatforms = $this->getPlatformIds($source);
        $candidatePlatforms = $this->getPlatformIds($candidate);

        return count(array_intersect($sourcePlatforms, $candidatePlatforms));
    }

    private function getGenreIds(Video $video): array
    {
        $genres = $video->getGame()?->getIgdbGame()?->getGenres();
        if (!$genres) {
            return [];
        }

        $genreIds = [];
        foreach ($genres as $genre) {
            $genreIds[] = $genre->getId();
        }
        return $genreIds;
    }

    private function getPlatformIds(Video $video): array
    {
        $platforms = $video->getGame()?->getPlatforms();
        if (!$platforms) {
            return [];
        }

        $platformIds = [];
        foreach ($platforms as $platform) {
            $platformIds[] = $platform->getId();
        }
        return $platformIds;
    }

    private function isRecentlyPopular(Video $video): bool
    {
        $thirtyDaysAgo = new \DateTime('-30 days');

        return $video->getCreatedAt() >= $thirtyDaysAgo &&
               $video->getViewCount() > 1000; // Seuil configurable
    }

    private function getPopularityScore(Video $video): float
    {
        // Score normalisé entre 0 et 1 basé sur vues et likes
        $viewScore = min(1.0, $video->getViewCount() / 100000); // Max à 100k vues
        $likeScore = min(1.0, $video->getLikeCount() / 1000);   // Max à 1k likes

        return ($viewScore + $likeScore) / 2;
    }

    private function getRecencyScore(Video $video): float
    {
        $daysSinceCreation = $video->getCreatedAt()->diff(new \DateTime())->days;

        // Score qui diminue avec le temps (max 1.0 pour vidéo du jour)
        return max(0, 1.0 - ($daysSinceCreation / 365)); // Linéaire sur 1 an
    }

    /**
     * Retourne un tableau des vidéos triées par score de pertinence
     */
    public function rankVideos(Video $sourceVideo, array $candidateVideos): array
    {
        $scoredVideos = [];

        foreach ($candidateVideos as $candidate) {
            $score = $this->calculateScore($sourceVideo, $candidate);
            $scoredVideos[] = [
                'video' => $candidate,
                'score' => $score
            ];
        }

        // Trier par score décroissant
        usort($scoredVideos, fn($a, $b) => $b['score'] <=> $a['score']);

        // Retourner seulement les vidéos (sans les scores)
        return array_map(fn($item) => $item['video'], $scoredVideos);
    }

    /**
     * Version debug qui retourne aussi les scores
     */
    public function rankVideosWithScores(Video $sourceVideo, array $candidateVideos): array
    {
        $scoredVideos = [];

        foreach ($candidateVideos as $candidate) {
            $score = $this->calculateScore($sourceVideo, $candidate);
            $scoredVideos[] = [
                'video' => $candidate,
                'score' => $score,
                'debug' => $this->getScoreBreakdown($sourceVideo, $candidate)
            ];
        }

        usort($scoredVideos, fn($a, $b) => $b['score'] <=> $a['score']);

        return $scoredVideos;
    }

    private function getScoreBreakdown(Video $source, Video $candidate): array
    {
        return [
            'same_game' => $this->isSameGame($source, $candidate) ? self::WEIGHTS['same_game'] : 0,
            'same_series' => $this->isSameSeries($source, $candidate) ? self::WEIGHTS['same_series'] : 0,
            'common_genres' => $this->getCommonGenresCount($source, $candidate) * self::WEIGHTS['genre_match'],
            'common_platforms' => $this->getCommonPlatformsCount($source, $candidate) * self::WEIGHTS['platform_match'],
            'recently_popular' => $this->isRecentlyPopular($candidate) ? self::WEIGHTS['popularity_recent'] : 0,
            'popularity_score' => $this->getPopularityScore($candidate) * self::WEIGHTS['popularity_all_time'],
            'recency_score' => $this->getRecencyScore($candidate) * self::WEIGHTS['recency_bonus'],
        ];
    }
}
