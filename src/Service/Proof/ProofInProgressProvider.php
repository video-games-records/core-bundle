<?php

namespace VideoGamesRecords\CoreBundle\Service\Proof;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Proof;

class ProofInProgressProvider
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em )
    {
        $this->em = $em;
    }

    /**
     * @return float|int|mixed|string
     */
    public function loadByGame()
    {
         $query = $this->em->createQueryBuilder()
            ->from('VideoGamesRecords\CoreBundle\Entity\Game', 'gam')
            ->select('gam')
            ->addSelect('COUNT(proof) as nb')
            ->innerJoin('gam.groups', 'grp')
            ->innerJoin('grp.charts', 'chr')
            ->innerJoin('chr.proofs', 'proof')
            ->where('proof.status = :status')
            ->setParameter('status', Proof::STATUS_IN_PROGRESS)
            ->groupBy('gam.id')
            ->orderBy('nb', 'DESC');

        return $query->getQuery()->getResult();
    }
}
