<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Badge;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\Handler\Badge\TeamMasterBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdateTeamMasterBadgeSubscriber implements EventSubscriberInterface
{
    private TeamMasterBadgeHandler $badgeHandler;

    public function __construct(TeamMasterBadgeHandler $badgeHandler)
    {
        $this->badgeHandler = $badgeHandler;
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
        $this->badgeHandler->process($event->getGame());
    }
}
