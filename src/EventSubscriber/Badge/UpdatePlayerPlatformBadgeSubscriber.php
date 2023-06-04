<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber\Badge;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\PlatformEvent;
use VideoGamesRecords\CoreBundle\Handler\Badge\PlayerPlatformBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdatePlayerPlatformBadgeSubscriber implements EventSubscriberInterface
{
    private PlayerPlatformBadgeHandler $badgeHandler;

    public function __construct(PlayerPlatformBadgeHandler $badgeHandler)
    {
        $this->badgeHandler = $badgeHandler;
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
        $this->badgeHandler->process($event->getPlatform());
    }
}
