<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;

class ProofRequestRepository extends EntityRepository
{

     /**
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countInProgress()
    {
        $qb = $this->createQueryBuilder('proof')
            ->select('COUNT(proof.id)')
            ->where('proof.status = :status')
            ->setParameter('status', ProofRequest::STATUS_IN_PROGRESS);

        return $qb->getQuery()
            ->getSingleScalarResult();
    }
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
