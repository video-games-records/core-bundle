<?php

namespace VideoGamesRecords\CoreBundle\Service\Dwh;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class DwhPlayerProvider
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
        $query = $this->em->createQuery(
            "
            SELECT p.id,
                   p.chartRank0,
                   p.chartRank1,
                   p.chartRank2,
                   p.chartRank3,
                   p.pointChart,
                   p.rankPointChart,
                   p.rankMedal,
                   p.nbChart,
                   p.pointGame,
                   p.rankPointGame                   
            FROM VideoGamesRecords\CoreBundle\Entity\Player p
            WHERE p.id <> 0"
        );
        return $query->getResult();
    }

    /**
     * @return array
     */
    public function getDataRank(): array
    {
        $query = $this->em->createQuery("
                    SELECT
                         p.id,
                         CASE WHEN pc.rank > 29 THEN 30 ELSE pc.rank END AS rank,
                         COUNT(pc.id) as nb
                    FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
                    JOIN pc.player p
                    WHERE pc.rank > 3            
                    GROUP BY p.id, rank");

        $result = $query->getResult();
        $data = array();
        foreach ($result as $row) {
            $data[$row['id']][$row['rank']] = $row['nb'];
        }
        return $data;
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
                 p.id,
                 COUNT(pc.chart) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            WHERE pc.lastUpdate BETWEEN :date1 AND :date2
            GROUP BY p.id");


        $query->setParameter('date1', $date1);
        $query->setParameter('date2', $date2);
        $result = $query->getResult();

        $data = array();
        foreach ($result as $row) {
            $data[$row['id']] = $row['nb'];
        }
        return $data;
    }
}

