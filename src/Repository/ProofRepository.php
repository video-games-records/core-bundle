<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\ValueObject\ProofStatus;

class ProofRepository extends EntityRepository
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
            ->setParameter('status', ProofStatus::STATUS_IN_PROGRESS);

        return $qb->getQuery()
            ->getSingleScalarResult();
    }
}
