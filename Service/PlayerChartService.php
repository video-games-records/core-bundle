<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartStatusRepository;

class PlayerChartService
{
    private GameService $gameService;
    private PlayerChartRepository $playerChartRepository;

    public function __construct(
        GameService $gameService,
        PlayerChartRepository $playerChartRepository,
    ) {
        $this->gameService = $gameService;
        $this->playerChartRepository = $playerChartRepository;
    }


    /**
     * @param $player
     * @param $game
     * @param $platform
     */
    public function majPlatform($player, $game, $platform) {
        // Update platform
        $this->playerChartRepository->majPlatform(
            $player,
            $game,
            $platform
        );
        // Maj all charts ot game
        $this->gameService->majChartStatus($game->getId());
    }
}
