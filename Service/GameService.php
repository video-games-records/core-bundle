<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Entity\GameDay;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class GameService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @param string $q
     * @param string $locale
     * @return mixed
     */
    public function autocomplete(string $q, string $locale)
    {
        return $this->em->getRepository('VideoGamesRecordsCoreBundle:Game')->autocomplete($q, $locale);
    }


    /**
     *
     */
    public function majChartRank()
    {
        $games = $this->em->getRepository('VideoGamesRecordsCoreBundle:Game')->findBy(array('boolMaj' => true));
        foreach ($games as $game) {
            $this->em->getRepository('VideoGamesRecordsCoreBundle:Chart')->majStatus($game);
            $game->setBoolMaj(false);
            $this->em->flush();
        }
    }

    /**
     *
     */
    public function addGameOfDay()
    {
        $now = new \Datetime();
        $gameDay = $this->em->getRepository('VideoGamesRecordsCoreBundle:GameDay')->findOneBy(array('day' => $now));
        if (!$gameDay) {
            $result = $this->em->getRepository('VideoGamesRecordsCoreBundle:Game')
                ->findBy(array('status' => 'ACTIF'));
            $games = array();
            foreach ($result as $game) {
                $games[] = $game;
            }
            $rand_key = array_rand($games, 1);
            $game = $games[$rand_key];

            $gameDay = new GameDay();
            $gameDay->setGame($game);
            $gameDay->setDay($now);
            $this->em->persist($gameDay);
            $this->em->flush();
        }
    }
}
