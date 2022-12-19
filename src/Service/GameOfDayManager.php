<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Datetime;
use VideoGamesRecords\CoreBundle\Entity\GameDay;
use VideoGamesRecords\CoreBundle\Repository\GameDayRepository;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;

class GameOfDayManager
{
    private GameDayRepository $gameDayRepository;
    private GameRepository $gameRepository;

    public function __construct(GameDayRepository $gameDayRepository, GameRepository $gameRepository)
    {
        $this->gameDayRepository = $gameDayRepository;
        $this->gameRepository = $gameRepository;
    }

    /**
     * @return void
     */
    public function add(): void
    {
        $tomorrow = new Datetime('tomorrow');
        $gameDay = $this->gameDayRepository->findOneBy(array('day' => $tomorrow));
        if (!$gameDay) {
            $games = $this->gameRepository->getIds();
            $rand_key = array_rand($games, 1);
            $game = $this->gameRepository->findOneBy($games[$rand_key]);
            $gameDay = new GameDay();
            $gameDay->setGame($game);
            $gameDay->setDay($tomorrow);
            $this->gameDayRepository->save($gameDay);
            $this->gameDayRepository->flush();
        }
    }
}
