<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Proof;

class ProofRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proof::class);
    }

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
            ->setParameter('status', Proof::STATUS_IN_PROGRESS);

        return $qb->getQuery()
            ->getSingleScalarResult();
    }
}
