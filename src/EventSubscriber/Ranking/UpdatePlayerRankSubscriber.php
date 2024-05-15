<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Ranking;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankUpdateInterface;
use VideoGamesRecords\CoreBundle\Ranking\Command\RankUpdate\PlayerRankUpdateHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdatePlayerRankSubscriber implements EventSubscriberInterface
{
    private RankUpdateInterface $rankUpdateHandler;

    public function __construct(
        #[Autowire(service: PlayerRankUpdateHandler::class)]
        RankUpdateInterface $rankUpdateHandler
    ) {
        $this->rankUpdateHandler = $rankUpdateHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::SCORES_PLAYER_MAJ_COMPLETED => 'process',
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
