<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\Service\Badge\PlayerMasterBadgeHandler;
use VideoGamesRecords\CoreBundle\Service\Badge\TeamMasterBadgeHandler;
use VideoGamesRecords\CoreBundle\Service\GameManager;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class GameSubscriber implements EventSubscriberInterface
{
    private PlayerMasterBadgeHandler $playerMasterBadgeHandler;
    private TeamMasterBadgeHandler $teamMasterBadgeHandler;
    private GameManager $gameManager;

    public function __construct(
        PlayerMasterBadgeHandler $playerMasterBadgeHandler,
        TeamMasterBadgeHandler $teamMasterBadgeHandler,
        GameManager $gameManager
    ) {
        $this->playerMasterBadgeHandler = $playerMasterBadgeHandler;
        $this->teamMasterBadgeHandler = $teamMasterBadgeHandler;
        $this->gameManager = $gameManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::PLAYER_GAME_MAJ_COMPLETED => 'playerGamePostMaj',
            VideoGamesRecordsCoreEvents::TEAM_GAME_MAJ_COMPLETED => 'teamGamePostMaj',
            VideoGamesRecordsCoreEvents::SCORE_PLATFORM_UPDATED => 'majGame',
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

    /**
     * @param GameEvent $event
     */
    public function majGame(GameEvent $event)
    {
        $this->gameManager->maj($event->getGame());
    }
}
