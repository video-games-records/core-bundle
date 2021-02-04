<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class ProofRequestRepository extends EntityRepository
{

    /**
     * @param $player
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNbRequestFromToDay($player)
    {
        $qb = $this->createQueryBuilder('request')
            ->select('COUNT(request)')
            ->where('request.playerRequesting = :player')
            ->setParameter('player', $player)
            ->andWhere('request.createdAt LIKE :now')
            ->setParameter('now', date('Y-m-d') . '%');

        return $qb->getQuery()
            ->getSingleScalarResult();
    }
}
