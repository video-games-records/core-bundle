<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Ranking;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankUpdateInterface;
use VideoGamesRecords\CoreBundle\Ranking\Command\RankUpdate\TeamRankUpdateHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdateTeamRankSubscriber implements EventSubscriberInterface
{
    private RankUpdateInterface $rankUpdateHandler;

    public function __construct(
        #[Autowire(service: TeamRankUpdateHandler::class)]
        RankUpdateInterface $rankUpdateHandler
    ) {
        $this->rankUpdateHandler = $rankUpdateHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::SCORES_TEAM_MAJ_COMPLETED => 'process',
        ];
    }


    public function process(): void
    {
        $this->rankUpdateHandler->majRankPointChart();
        $this->rankUpdateHandler->majRankPointGame();
        $this->rankUpdateHandler->majRankCup();
        $this->rankUpdateHandler->majRankMedal();
        $this->rankUpdateHandler->majRankBadge();
    }
}
