<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Video;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Service\VideoRecommendationService;

#[AsController]
class GetRelatedVideosDebug extends AbstractController
{
    public function __construct(
        private readonly VideoRecommendationService $videoRecommendationService
    ) {
    }

    public function __invoke(Video $data, Request $request): JsonResponse
    {
        $limit = min(20, max(1, (int) $request->query->get('limit', 10)));

        $scoredRecommendations = $this->videoRecommendationService->getRecommendationsWithScores($data, $limit);

        $response = [
            'video_id' => $data->getId(),
            'source_video' => [
                'title' => $data->getTitle(),
                'game' => $data->getGame()?->getLibGameEn(),
                'series' => $data->getGame()?->getSerie()?->getLibSerie(),
                'genres' => $this->getGenreNames($data),
                'platforms' => $this->getPlatformNames($data),
            ],
            'scored_recommendations' => array_map(function ($item) {
                return [
                    'video' => [
                        'id' => $item['video']->getId(),
                        'title' => $item['video']->getTitle(),
                        'game' => $item['video']->getGame()?->getLibGameEn(),
                        'series' => $item['video']->getGame()?->getSerie()?->getLibSerie(),
                        'view_count' => $item['video']->getViewCount(),
                        'like_count' => $item['video']->getLikeCount(),
                    ],
                    'score' => $item['score'],
                    'score_breakdown' => $item['debug']
                ];
            }, array_slice($scoredRecommendations, 0, $limit))
        ];

        return new JsonResponse($response);
    }

    private function getGenreNames(Video $video): array
    {
        $genres = $video->getGame()?->getIgdbGame()?->getGenres();
        if (!$genres) {
            return [];
        }

        $names = [];
        foreach ($genres as $genre) {
            $names[] = $genre->getName();
        }
        return $names;
    }

    private function getPlatformNames(Video $video): array
    {
        $platforms = $video->getGame()?->getPlatforms();
        if (!$platforms) {
            return [];
        }

        $names = [];
        foreach ($platforms as $platform) {
            $names[] = $platform->getName();
        }
        return $names;
    }
}
