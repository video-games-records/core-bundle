<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber\Badge;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\Service\Badge\PlayerMasterBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdatePlayerMasterBadgeSubscriber implements EventSubscriberInterface
{
    private PlayerMasterBadgeHandler $playerMasterBadgeHandler;

    public function __construct(PlayerMasterBadgeHandler $playerMasterBadgeHandler)
    {
        $this->playerMasterBadgeHandler = $playerMasterBadgeHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::PLAYER_GAME_MAJ_COMPLETED => 'process',
        ];
    }

    /**
     * @param GameEvent $event
     */
    public function process(GameEvent $event): void
    {
        $this->playerMasterBadgeHandler->process($event->getGame());
    }
}
