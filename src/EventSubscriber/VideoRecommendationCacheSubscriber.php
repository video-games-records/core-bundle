<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Service\VideoRecommendationService;

class VideoRecommendationCacheSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly VideoRecommendationService $videoRecommendationService
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Video) {
            $this->videoRecommendationService->clearVideoRecommendationsCache($entity);
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Video) {
            $this->videoRecommendationService->clearVideoRecommendationsCache($entity);
        }
    }
}
