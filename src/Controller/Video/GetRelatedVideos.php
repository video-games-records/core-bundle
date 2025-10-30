<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Video;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Service\VideoRecommendationService;

#[AsController]
class GetRelatedVideos extends AbstractController
{
    public function __construct(
        private readonly VideoRecommendationService $videoRecommendationService
    ) {
    }

    public function __invoke(Video $data, Request $request): array
    {
        $limit = min(20, max(1, (int) $request->query->get('limit', 10)));

        return $this->videoRecommendationService->getRelatedVideos($data, $limit);
    }
}
