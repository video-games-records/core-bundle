<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class Player
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $player
     * @return mixed
     */
    public function getNbLostPosition($player)
    {
        return $this->em->getRepository('VideoGamesRecordsCoreBundle:LostPosition')->getNbLostPosition($player);
    }

    /**
     * @param $player
     * @return mixed
     */
    public function getNbNewLostPosition($player)
    {
        return $this->em->getRepository('VideoGamesRecordsCoreBundle:LostPosition')->getNbNewLostPosition($player);
    }

    /**
     * @param $player
     * @return mixed
     */
    public function getGameStats($player)
    {
        $playerGames = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getFromPlayer($player);
        $stats = $this->em->getRepository('VideoGamesRecordsCoreBundle:Game')->getStatsFromPlayer($player);

        foreach ($playerGames as $playerGame) {
            $playerGame->setStatuses($stats[$playerGame->getGame()->getId()]);
        }
        return $playerGames;
    }
}
