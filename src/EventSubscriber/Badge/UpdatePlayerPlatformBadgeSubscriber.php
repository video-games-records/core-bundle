<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber\Badge;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\PlatformEvent;
use VideoGamesRecords\CoreBundle\Service\Badge\PlatformBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdatePlayerPlatformBadgeSubscriber implements EventSubscriberInterface
{
    private PlatformBadgeHandler $platformBadgeHandler;

    public function __construct(PlatformBadgeHandler $platformBadgeHandler)
    {
        $this->platformBadgeHandler = $platformBadgeHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::PLATFORM_MAJ_COMPLETED => 'process',
        ];
    }

    /**
     * @param PlatformEvent $event
     */
    public function process(PlatformEvent $event)
    {
        $this->platformBadgeHandler->process($event->getPlatform());
    }
}
