<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Badge;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\SerieEvent;
use VideoGamesRecords\CoreBundle\Handler\Badge\TeamSerieBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdateTeamSerieBadgeSubscriber implements EventSubscriberInterface
{
    private TeamSerieBadgeHandler $badgeHandler;

    public function __construct(TeamSerieBadgeHandler $badgeHandler)
    {
        $this->badgeHandler = $badgeHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::TEAM_SERIE_MAJ_COMPLETED => 'process',
        ];
    }

    /**
     * @param SerieEvent $event
     */
    public function process(SerieEvent $event)
    {
        $this->badgeHandler->process($event->getSerie());
    }
}
