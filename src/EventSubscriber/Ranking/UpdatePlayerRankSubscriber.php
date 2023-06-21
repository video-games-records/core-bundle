<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber\Ranking;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankUpdateInterface;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdatePlayerRankSubscriber implements EventSubscriberInterface
{
    private RankUpdateInterface $rankUpdateHandler;

    public function __construct(RankUpdateInterface $rankUpdateHandler)
    {
        $this->rankUpdateHandler = $rankUpdateHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::SCORES_PLAYER_MAJ_COMPLETED => 'process',
        ];
    }

    /**
     * @param Event $event
     */
    public function process(Event $event): void
    {
        $this->rankUpdateHandler->majRankPointChart();
        $this->rankUpdateHandler->majRankPointGame();
        $this->rankUpdateHandler->majRankCup();
        $this->rankUpdateHandler->majRankMedal();
        $this->rankUpdateHandler->majRankBadge();
    }
}
