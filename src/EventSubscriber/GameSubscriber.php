<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\Manager\GameManager;
use VideoGamesRecords\CoreBundle\Service\Badge\PlayerMasterBadgeHandler;
use VideoGamesRecords\CoreBundle\Service\Badge\TeamMasterBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class GameSubscriber implements EventSubscriberInterface
{
    private GameManager $gameManager;

    public function __construct(GameManager $gameManager)
    {
        $this->gameManager = $gameManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::SCORE_PLATFORM_UPDATED => 'majGame',
        ];
    }


    /**
     * @param GameEvent $event
     */
    public function majGame(GameEvent $event): void
    {
        $this->gameManager->maj($event->getGame());
    }
}
