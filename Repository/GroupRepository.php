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
            ->leftJoin('gr.translations', 'gr_translation')
            ->addSelect('gr_translation')
            ->join('gr.game', 'ga')
            ->addSelect('ga')
            ->leftJoin('ga.translations', 'ga_translation')
            ->addSelect('ga_translation')
            ->where('gr.id = :idGroup')
            ->setParameter('idGroup', $id);

        return $query->getQuery()
            ->getOneOrNullResult();
    }
}
