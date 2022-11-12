<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\CountryEvent;
use VideoGamesRecords\CoreBundle\Service\Badge\CountryBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class CountrySubscriber implements EventSubscriberInterface
{
    private CountryBadgeHandler $countryBadgeHandler;

    public function __construct(CountryBadgeHandler $countryBadgeHandler)
    {
        $this->countryBadgeHandler = $countryBadgeHandler;

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
        $this->countryBadgeHandler->process($event->getCountry());
    }
}
