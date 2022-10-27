<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Service\Badge\PlayerMasterBadgeUpdater;
use VideoGamesRecords\CoreBundle\Service\Badge\TeamMasterBadgeUpdater;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;
use VideoGamesRecords\CoreBundle\Event\GameEvent;

final class GameSubscriber implements EventSubscriberInterface
{
    private PlayerMasterBadgeUpdater $playerMasterBadgeUpdater;
    private TeamMasterBadgeUpdater $teamMasterBadgeUpdater;

    public function __construct(PlayerMasterBadgeUpdater $playerMasterBadgeUpdater, TeamMasterBadgeUpdater $teamMasterBadgeUpdater)
    {
        $this->playerMasterBadgeUpdater = $playerMasterBadgeUpdater;
        $this->teamMasterBadgeUpdater = $teamMasterBadgeUpdater;
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
        $this->playerMasterBadgeUpdater->process($event->getGame());
    }

    /**
     * @param GameEvent $event
     */
    public function teamGamePostMaj(GameEvent $event)
    {
        $this->teamMasterBadgeUpdater->process($event->getGame());
    }
}
