<?php

namespace VideoGamesRecords\CoreBundle\Service\Dwh;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DwhTeamProvider
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return mixed
     */
    public function getDataForDwh(): mixed
    {
        $query = $this->em->createQuery("
            SELECT t.id,
                   t.pointChart,
                   t.pointBadge,
                   t.chartRank0,
                   t.chartRank1,
                   t.chartRank2,
                   t.chartRank3,
                   t.rankPointChart,
                   t.rankMedal,
                   t.rankBadge,
                   t.rankCup,
                   t.gameRank0,
                   t.gameRank1,
                   t.gameRank2,
                   t.gameRank3,
                   t.nbMasterBadge,
                   t.pointGame,
                   t.rankPointGame                  
            FROM VideoGamesRecords\CoreBundle\Entity\Team t");
        return $query->getResult();
    }

      /**
     * @param DateTime $date1
     * @param DateTime $date2
     * @return array
     */
    public function getNbPostDay(DateTime $date1, DateTime $date2): array
    {
        $query = $this->em->createQuery("
            SELECT
                 t.id,
                 COUNT(pc.id) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN p.team t
            WHERE pc.lastUpdate BETWEEN :date1 AND :date2
            GROUP BY t.id");


        $query->setParameter('date1', $date1);
        $query->setParameter('date2', $date2);
        $result = $query->getResult();

        $data = array();
        foreach ($result as $row) {
            $data[$row['idTeam']] = $row['nb'];
        }
        return $data;
    }
}

