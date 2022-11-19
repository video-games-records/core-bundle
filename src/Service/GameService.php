<?php

namespace VideoGamesRecords\CoreBundle\Service;

use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;

class GameService
{
    private GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @param int    $idGame
     * @param string $status
     */
    public function majChartStatus(int $idGame, string $status = 'MAJ'): void
    {
        $game = $this->getGame($idGame);
        if ($game) {
            $this->gameRepository->majChartStatus($game, $status);
        }
    }

    /**
     * @param $idGame
     * @return \VideoGamesRecords\CoreBundle\Service\Game|null
     */
    private function getGame($idGame) : ?Game
    {
        return $this->gameRepository->find($idGame);
    }
}
