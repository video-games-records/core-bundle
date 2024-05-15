<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Contracts\VgrCoreInterface;
use VideoGamesRecords\CoreBundle\Entity\Player;

class CanAskProofProvider implements VgrCoreInterface
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Player $player
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function load(Player $player): bool
    {
        $qb = $this->em->createQueryBuilder()
            ->from('VideoGamesRecords\CoreBundle\Entity\ProofRequest', 'request')
            ->select('COUNT(request)')
            ->where('request.playerRequesting = :player')
            ->setParameter('player', $player)
            ->andWhere('request.createdAt LIKE :now')
            ->setParameter('now', date('Y-m-d') . '%');

        $nb = $qb->getQuery()->getSingleScalarResult();

        if ($nb >= self::MAX_PROOF_REQUEST_DAY) {
            return false;
        }
        return true;
    }
}
