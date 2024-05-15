<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Manager;

use Datetime;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\GameDay;

class GameOfDayManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return void
     */
    public function add(): void
    {
        $tomorrow = new Datetime('tomorrow');
        $gameDay = $this->em->getRepository(GameDay::class)->findOneBy(array('day' => $tomorrow));
        if (!$gameDay) {
            $games = $this->em->getRepository(Game::class)->getIds();
            $rand_key = array_rand($games, 1);
            $game = $this->em->getRepository(Game::class)->findOneBy($games[$rand_key]);
            $gameDay = new GameDay();
            $gameDay->setGame($game);
            $gameDay->setDay($tomorrow);
            $this->em->persist($gameDay);
            $this->em->flush();
        }
    }
}
