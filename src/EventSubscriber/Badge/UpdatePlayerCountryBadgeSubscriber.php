<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Badge;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\CountryEvent;
use VideoGamesRecords\CoreBundle\Handler\Badge\PlayerCountryBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdatePlayerCountryBadgeSubscriber implements EventSubscriberInterface
{
    private PlayerCountryBadgeHandler $badgeHandler;

    public function __construct(PlayerCountryBadgeHandler $badgeHandler)
    {
        $this->badgeHandler = $badgeHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::COUNTRY_MAJ_COMPLETED => 'process',
        ];
    }

    /**
     * @param CountryEvent $event
     */
    public function process(CountryEvent $event): void
    {
        $this->badgeHandler->process($event->getCountry());
    }
}
