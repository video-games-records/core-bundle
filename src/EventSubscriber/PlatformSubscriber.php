<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\PlatformEvent;
use VideoGamesRecords\CoreBundle\Service\Badge\PlatformBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class PlatformSubscriber implements EventSubscriberInterface
{
    private PlatformBadgeHandler $platformBadgeHandler;

    public function __construct(PlatformBadgeHandler $platformBadgeHandler)
    {
        $this->platformBadgeHandler = $platformBadgeHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::PLATFORM_MAJ_COMPLETED => 'platformPostMaj',
        ];
    }

    /**
     * @param PlatformEvent $event
     */
    public function playerGamePostMaj(PlatformEvent $event)
    {
        $this->platformBadgeHandler->process($event->getPlatform());
    }
}
