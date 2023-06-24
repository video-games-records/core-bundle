<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class PlayerChartRepository extends EntityRepository
{
    /**
     * @param Chart $chart
     * @return int|mixed|string
     */
    public function getPlatforms(Chart $chart)
    {
        $query = $this->_em->createQuery("
            SELECT
                 p.id,
                 COUNT(pc) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            INNER JOIN pc.platform p
            WHERE pc.chart = :chart
            GROUP BY p.id");

        $query->setParameter('chart', $chart);
        return $query->getResult(2);
    }
}
