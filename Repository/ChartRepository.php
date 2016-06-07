<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ChartRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChartRepository extends EntityRepository
{
    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getWithGame($id)
    {
        $query = $this->createQueryBuilder('ch')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->join('gr.game', 'ga')
            ->addSelect('ga')
            ->where('ch.idChart = :idChart')
            ->setParameter('idChart', $id);

        return $query->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getWithChartType($id)
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.libs', 'lib')
            ->addSelect('lib')
            ->join('lib.type', 'type')
            ->addSelect('type')
            ->where('c.idChart = :idChart')
            ->setParameter('idChart', $id);

        return $query->getQuery()->getOneOrNullResult();
    }


}
