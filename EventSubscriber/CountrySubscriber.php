<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\CountryEvent;
use VideoGamesRecords\CoreBundle\Service\Badge\CountryBadgeUpdater;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class CountrySubscriber implements EventSubscriberInterface
{
    private CountryBadgeUpdater $countryBadgeUpdater;

    public function __construct(CountryBadgeUpdater $countryBadgeUpdater)
    {
        $this->countryBadgeUpdater = $countryBadgeUpdater;

    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::COUNTRY_MAJ_COMPLETED => 'countryPostMaj',
        ];
    }

    /**
     * @param CountryEvent $event
     */
    public function countryPostMaj(CountryEvent $event)
    {
        $this->countryBadgeUpdater->process($event->getCountry());
    }
}
