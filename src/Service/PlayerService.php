<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;
use VideoGamesRecords\CoreBundle\Repository\ProofRequestRepository;

class PlayerService
{
    private PlayerRepository $playerRepository;
    private ProofRequestRepository $proofRequestRepository;

    public function __construct(
        PlayerRepository $playerRepository,
        ProofRequestRepository $proofRequestRepository
    )
    {
        $this->playerRepository = $playerRepository;
        $this->proofRequestRepository = $proofRequestRepository;
    }

     public function autocomplete($q)
    {
        return $this->playerRepository->autocomplete($q);
    }

    /**
     * @param Player $player
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function canAskProof(Player $player): bool
    {
        $nb = $this->proofRequestRepository->getNbRequestFromToDay($player);
        if ($nb >= 3) {
             return false;
        }
        return true;
    }
}
