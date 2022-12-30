<?php

namespace VideoGamesRecords\CoreBundle\Service\Stats\Write;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameStatsHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Game $game): void
    {
        $nbChart = 0;
        $nbPost = 0;
        foreach ($game->getGroups() as $group) {
            $nbChart += $group->getNbChart();
            $nbPost += $group->getNbPost();
        }
        $game->setNbChart($nbChart);
        $game->setNbPost($nbPost);
        $this->em->flush();
    }
}
