<?php

namespace VideoGamesRecords\CoreBundle\Service\Dwh;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Interface\Dwh\DwhTableProviderInterface;

class DwhGameProvider implements DwhTableProviderInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getDataForDwh(): array
    {
        return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->findAll();
    }

    /**
     * @param $date1
     * @param $date2
     * @return array
     */
    public function getNbPostDay($date1, $date2): array
    {
        //----- data nbPostDay
        $query = $this->em->createQuery("
            SELECT
                 ga.id,
                 COUNT(pc.id) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            JOIN c.group gr
            JOIN gr.game ga
            WHERE pc.lastUpdate BETWEEN :date1 AND :date2
            GROUP BY ga.id");


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

