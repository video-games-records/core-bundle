<?php

namespace VideoGamesRecords\CoreBundle\Controller\Player\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;

class GetStats extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Player $player
     * @return mixed
     */
    public function __invoke(Player $player): mixed
    {
        $query = $this->em->createQuery("
            SELECT
                 s,
                 COUNT(pc) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus s
            JOIN s.playerCharts pc
            WHERE pc.player = :player
            GROUP BY s.id");

        $query->setParameter('player', $player);
        return $query->getResult();
    }
}
