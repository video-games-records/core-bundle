<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class ScorePlatformManager
{
    private PlayerChartRepository $playerChartRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(PlayerChartRepository $playerChartRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->playerChartRepository = $playerChartRepository;
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * @param Player   $player
     * @param Game     $game
     * @param Platform $platform
     * @return void
     */
    public function update(Player $player, Game $game, Platform $platform): void
    {
        // Update platform
        $this->playerChartRepository->majPlatform(
            $player, $game, $platform
        );

        $event = new GameEvent($game);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::SCORE_PLATFORM_UPDATED);
    }
}
