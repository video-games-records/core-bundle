<?php

namespace VideoGamesRecords\CoreBundle\Service\Stats\Write;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameStatsHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function handle(Game $game): void
    {
        $this->majNbChart($game);
        $this->majNbPost($game);
        $this->majNbPlayer($game);
    }

    public function majNbChart(Game $game): void
    {
        $nbChart = 0;
        foreach ($game->getGroups() as $group) {
            $nbChart += $group->getNbChart();
        }
        $game->setNbChart($nbChart);
        $this->em->flush();
    }

     public function majNbPost(Game $game): void
    {
        $nbPost = 0;
        foreach ($game->getGroups() as $group) {
            $nbPost += $group->getNbPost();
        }
        $game->setNbPost($nbPost);
        $this->em->flush();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function majNbPlayer(Game $game): void
    {
        $query = $this->em->createQuery("
            SELECT COUNT(DISTINCT pc.player)
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            JOIN c.group g
            WHERE g.game = :game");
        $query->setParameter('game', $game);

        $nb = $query->getSingleScalarResult();
        $game->setNbPlayer($nb);
        $this->em->flush();
    }
}
