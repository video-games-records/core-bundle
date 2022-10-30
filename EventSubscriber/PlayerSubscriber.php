<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\PlayerEvent;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;


final class PlayerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::PLAYER_MAJ_COMPLETED => 'playerPostMaj',
        ];
    }

    /**
     * @param PlayerEvent $event
     */
    public function playerPostMaj(PlayerEvent $event)
    {
        $player = $event->getPlayer();
        $player->getCountry()
            ?->setBoolMaj(true);
    }
}
