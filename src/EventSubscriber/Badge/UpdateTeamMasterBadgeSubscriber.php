<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber\Badge;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\Service\Badge\TeamMasterBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdateTeamMasterBadgeSubscriber implements EventSubscriberInterface
{
    private TeamMasterBadgeHandler $teamMasterBadgeHandler;

    public function __construct(TeamMasterBadgeHandler $teamMasterBadgeHandler)
    {
        $this->teamMasterBadgeHandler = $teamMasterBadgeHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::TEAM_GAME_MAJ_COMPLETED => 'process',
        ];
    }


    /**
     * @param GameEvent $event
     */
    public function process(GameEvent $event): void
    {
        $this->teamMasterBadgeHandler->process($event->getGame());
    }
}
