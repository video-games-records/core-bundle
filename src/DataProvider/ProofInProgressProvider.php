<?php

namespace VideoGamesRecords\CoreBundle\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\ValueObject\ProofStatus;

class ProofInProgressProvider
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return float|int|mixed|string
     */
    public function loadByGame(): mixed
    {
        $query = $this->em->createQueryBuilder()
            ->from('VideoGamesRecords\CoreBundle\Entity\Game', 'gam')
            ->select('gam')
            ->addSelect('COUNT(proof) as nb')
            ->innerJoin('gam.groups', 'grp')
            ->innerJoin('grp.charts', 'chr')
            ->innerJoin('chr.proofs', 'proof')
            ->where('proof.status = :status')
            ->setParameter('status', ProofStatus::STATUS_IN_PROGRESS)
            ->groupBy('gam.id')
            ->orderBy('nb', 'DESC');

        return $query->getQuery()->getResult();
    }
}
