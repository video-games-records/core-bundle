<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\GameDay;
use VideoGamesRecords\CoreBundle\Repository\GameDayRepository;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerGameRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamGameRepository;

class GameService
{
    private GameRepository $gameRepository;
    private GameDayRepository $gameDayRepository;
    private PlayerGameRepository $playerGameRepository;
    private TeamGameRepository $teamGameRepository;
    private PlayerBadgeRepository $playerBadgeRepository;
    private TeamBadgeRepository $teamBadgeRepository;

    public function __construct(
        GameRepository $gameRepository,
        GameDayRepository $gameDayRepository,
        PlayerGameRepository $playerGameRepository,
        TeamGameRepository $teamGameRepository,
        PlayerBadgeRepository $playerBadgeRepository,
        TeamBadgeRepository $teamBadgeRepository
    )
    {
        $this->gameRepository = $gameRepository;
        $this->gameDayRepository = $gameDayRepository;
        $this->playerGameRepository = $playerGameRepository;
        $this->teamGameRepository = $teamGameRepository;
        $this->playerBadgeRepository = $playerBadgeRepository;
        $this->teamBadgeRepository = $teamBadgeRepository;
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
     * @throws ORMException
     */
    public function addGameOfDay()
    {
        $now = new \Datetime();
        $gameDay = $this->gameDayRepository->findOneBy(array('day' => $now));
        if (!$gameDay) {
            $games = $this->gameRepository->getIds();
            $rand_key = array_rand($games, 1);
            $game = $this->gameRepository->findOneBy($games[$rand_key]);
            $gameDay = new GameDay();
            $gameDay->setGame($game);
            $gameDay->setDay($now);
            $this->gameDayRepository->save($gameDay);
            $this->gameDayRepository->flush();
        }
    }

    /**
     * @param int $idGame
     * @throws Exception
     */
    public function majPlayerMasterBadge(int $idGame)
    {
        $game = $this->getGame($idGame);
        if ($game) {
            $this->playerBadgeRepository->majMasterBadge($game);
        }
    }

     /**
     * @param int $idGame
     * @throws Exception
     */
    public function majTeamMasterBadge(int $idGame)
    {
        $game = $this->getGame($idGame);
        if ($game) {
            $this->teamBadgeRepository->majMasterBadge($game);
        }
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
     * @return Game|null
     * @throws NonUniqueResultException
     */
    public function getGameOfDay(): ?Game
    {
        return $this->gameRepository->getGameOfday();
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
