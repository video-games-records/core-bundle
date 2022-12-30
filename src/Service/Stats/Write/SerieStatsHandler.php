<?php

namespace VideoGamesRecords\CoreBundle\Service\Stats\Write;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class SerieStatsHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Serie $serie): void
    {
        $serie->setNbGame(count($serie->getGames()));
        $nbChart = 0;
        foreach ($serie->getGames() as $game) {
            $nbChart += $game->getNbChart();
        }
        $serie->setNbChart($nbChart);
        $this->em->flush();
    }
}
