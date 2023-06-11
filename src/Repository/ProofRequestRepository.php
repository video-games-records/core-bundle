<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\ValueObject\ProofRequestStatus;

class ProofRequestRepository extends EntityRepository
{
    /**
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countInProgress(): mixed
    {
        $qb = $this->createQueryBuilder('proof')
            ->select('COUNT(proof.id)')
            ->where('proof.status = :status')
            ->setParameter('status', ProofRequestStatus::STATUS_IN_PROGRESS);

        return $qb->getQuery()
            ->getSingleScalarResult();
    }
}
