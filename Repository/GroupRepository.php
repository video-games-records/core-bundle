<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    /**
     * @param int $id
     * @return \VideoGamesRecords\CoreBundle\Entity\Group|null
     */
    public function getWithGame($id)
    {
        $query = $this->createQueryBuilder('gr')
            ->join('gr.game', 'ga')
            ->addSelect('ga')
            ->where('gr.idGroup = :idGroup')
            ->setParameter('idGroup', $id);

        return $query->getQuery()
            ->getOneOrNullResult();
    }
}
