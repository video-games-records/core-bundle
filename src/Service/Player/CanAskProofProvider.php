<?php

namespace VideoGamesRecords\CoreBundle\Service\Player;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Repository\ProofRequestRepository;

class CanAskProofProvider
{
    public const MAX_REQUEST_DAY = 5;
    private ProofRequestRepository $proofRequestRepository;

    public function __construct(ProofRequestRepository $proofRequestRepository) {
        $this->proofRequestRepository = $proofRequestRepository;
    }

    /**
     * @param Player $player
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function load(Player $player): bool
    {
        $nb = $this->proofRequestRepository->countPlayerToDay($player);
        if ($nb >= self::MAX_REQUEST_DAY) {
            return false;
        }
        return true;
    }
}
