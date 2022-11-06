<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Service\Badge\PlayerMasterBadgeHandler;
use VideoGamesRecords\CoreBundle\Service\Badge\TeamMasterBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;
use VideoGamesRecords\CoreBundle\Event\GameEvent;

final class GameSubscriber implements EventSubscriberInterface
{
    private PlayerMasterBadgeHandler $playerMasterBadgeHandler;
    private TeamMasterBadgeHandler $teamMasterBadgeHandler;

    public function __construct(PlayerMasterBadgeHandler $playerMasterBadgeHandler, TeamMasterBadgeHandler $teamMasterBadgeHandler)
    {
        $this->playerMasterBadgeHandler = $playerMasterBadgeHandler;
        $this->teamMasterBadgeHandler = $teamMasterBadgeHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::PLAYER_GAME_MAJ_COMPLETED => 'playerGamePostMaj',
            VideoGamesRecordsCoreEvents::TEAM_GAME_MAJ_COMPLETED => 'teamGamePostMaj',
        ];
    }

    /**
     * @param GameEvent $event
     */
    public function playerGamePostMaj(GameEvent $event)
    {
        $this->playerMasterBadgeHandler->process($event->getGame());
    }

    /**
     * @param GameEvent $event
     */
    public function teamGamePostMaj(GameEvent $event)
    {
        $this->teamMasterBadgeHandler->process($event->getGame());
    }
}
