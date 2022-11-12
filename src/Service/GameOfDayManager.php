<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\ORMException;
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
     * @throws ORMException
     */
    public function add(): void
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
}
