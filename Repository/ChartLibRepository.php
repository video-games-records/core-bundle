<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;

class ChartLibRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartLib::class);
    }

    /**
     * @param $params
     * @return array
     */
    public function getWithType($params)
    {
        $query = $this->createQueryBuilder('cl')
            ->join('cl.type', 't')
            ->addSelect('t');

        if (array_key_exists('idChart', $params)) {
            $query->where('cl.idChart = :idChart')
                ->setParameter('idChart', $params['idChart']);
        }

        /*if (array_key_exists('idGroup', $params)) {
        }*/

        return $query->getQuery()
            ->getResult();
    }
}
