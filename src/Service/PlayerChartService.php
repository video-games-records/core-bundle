<?php

namespace VideoGamesRecords\CoreBundle\Service;

use VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository;

class PlayerChartService
{
    private GameService $gameService;
    private PlayerChartRepository $playerChartRepository;

    public function __construct(GameService $gameService, PlayerChartRepository $playerChartRepository)
    {
        $this->gameService = $gameService;
        $this->playerChartRepository = $playerChartRepository;
    }


    /**
     * @param $player
     * @param $game
     * @param $platform
     */
    public function majPlatform($player, $game, $platform)
    {
        // Update platform
        $this->playerChartRepository->majPlatform(
            $player, $game, $platform
        );
        // Maj all charts ot game
        $this->gameService->majChartStatus($game->getId());
    }
}
