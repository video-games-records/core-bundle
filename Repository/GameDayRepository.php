<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GameDayRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function getMax()
    {
        $qb = $this->createQueryBuilder('gd')
            ->select('max(gd.day)');
        $result = $qb->getQuery()->getSingleResult();
        return $result[1];
    }
}
