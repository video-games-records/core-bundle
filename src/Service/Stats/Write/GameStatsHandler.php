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
