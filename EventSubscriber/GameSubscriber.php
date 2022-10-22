<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Service\Badge\PlayerMasterBadge;
use VideoGamesRecords\CoreBundle\Service\Badge\TeamMasterBadge;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;
use VideoGamesRecords\CoreBundle\Event\GameEvent;

final class GameSubscriber implements EventSubscriberInterface
{
    private PlayerMasterBadge $playerMasterBadge;
    private TeamMasterBadge $teamMasterBadge;

    public function __construct(PlayerMasterBadge $playerMasterBadge, TeamMasterBadge $teamMasterBadge)
    {
        $this->playerMasterBadge = $playerMasterBadge;
        $this->teamMasterBadge = $teamMasterBadge;
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
        $this->playerMasterBadge->maj($event->getGame());
    }

    /**
     * @param GameEvent $event
     */
    public function teamGamePostMaj(GameEvent $event)
    {
        $this->teamMasterBadge->maj($event->getGame());
    }
}
