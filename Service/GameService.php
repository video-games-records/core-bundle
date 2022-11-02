<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\GameDay;
use VideoGamesRecords\CoreBundle\Repository\GameDayRepository;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;

class GameService
{
    private GameRepository $gameRepository;

    public function __construct(
        GameRepository $gameRepository,
    )
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @param string $q
     * @param string $locale
     * @return mixed
     */
    public function autocomplete(string $q, string $locale)
    {
        return $this->gameRepository->autocomplete($q, $locale);
    }

    /**
     * @param int    $idGame
     * @param string $status
     */
    public function majChartStatus(int $idGame, string $status = 'MAJ')
    {
        $game = $this->getGame($idGame);
        if ($game) {
            $this->gameRepository->majChartStatus($game, $status);
        }
    }

    /**
     * @param $idGame
     * @return Game|null
     */
    private function getGame($idGame) : ?Game
    {
        return $this->gameRepository->find($idGame);
    }
}
