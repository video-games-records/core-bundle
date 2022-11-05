<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Repository\LostPositionRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;
use VideoGamesRecords\CoreBundle\Repository\ProofRequestRepository;

class PlayerService
{
    private EntityManagerInterface $em;
    private PlayerRepository $playerRepository;
    private ProofRequestRepository $proofRequestRepository;

    public function __construct(
        EntityManagerInterface $em,
        PlayerRepository $playerRepository,
        ProofRequestRepository $proofRequestRepository
    )
    {
        $this->em = $em;
        $this->playerRepository = $playerRepository;
        $this->proofRequestRepository = $proofRequestRepository;
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


    public function autocomplete($q)
    {
        return $this->playerRepository->autocomplete($q);
    }


    /**
     *
     */
    public function majRulesOfThree(): void
    {
        $group1 = $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\User\GroupInterface', 2);
        $group2 = $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\User\GroupInterface', 9);

        $players = $this->playerRepository->getPlayerToDisabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->removeGroup($group1);
            $user->addGroup($group2);
        }
        $this->em->flush();

        $players = $this->playerRepository->getPlayerToEnabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->addGroup($group1);
            $user->removeGroup($group2);
        }
        $this->em->flush();
    }
}
