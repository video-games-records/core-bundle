<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ChartRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChartLibRepository extends EntityRepository
{
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

        if (array_key_exists('idGroup', $params)) {
        }

        return $query->getQuery()
            ->getResult();
    }

}