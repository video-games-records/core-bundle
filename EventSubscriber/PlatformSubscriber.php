<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Service\Badge\PlatformBadgeUpdater;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;
use VideoGamesRecords\CoreBundle\Event\PlatformEvent;

final class PlatformSubscriber implements EventSubscriberInterface
{
    private PlatformBadgeUpdater $platformBadgeUpdater;

    public function __construct(PlatformBadgeUpdater $platformBadgeUpdater)
    {
        $this->platformBadgeUpdater = $platformBadgeUpdater;
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
        $this->platformBadgeUpdater->process($event->getPlatform());
    }
}
