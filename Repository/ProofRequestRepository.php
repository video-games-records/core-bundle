<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProofRequestRepository extends EntityRepository
{

    /**
     * @param $player
     * @return mixed
     */
    public function getNbRequestFromToDay($player)
    {
        $qb = $this->createQueryBuilder('request')
            ->select('COUNT(request.idRequest)')
            ->where('request.playerRequesting = :player')
            ->setParameter('player', $player)
            ->andWhere('request.createdAt LIKE :now')
            ->setParameter('now', date('Y-m-d') . '%');

        return $qb->getQuery()
            ->getSingleScalarResult();
    }
}
