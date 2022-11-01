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
    private LostPositionRepository $lostPositionRepository;
    private ProofRequestRepository $proofRequestRepository;

    public function __construct(
        EntityManagerInterface $em,
        PlayerRepository $playerRepository,
        LostPositionRepository $lostPositionRepository,
        ProofRequestRepository $proofRequestRepository
    )
    {
        $this->em = $em;
        $this->playerRepository = $playerRepository;
        $this->lostPositionRepository = $lostPositionRepository;
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
     * @param $player
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNbLostPosition($player)
    {
        return $this->lostPositionRepository->getNbLostPosition($player);
    }

    /**
     * @param $player
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNbNewLostPosition($player)
    {
        if ($player->getLastDisplayLostPosition() != null) {
            return $this->lostPositionRepository->getNbNewLostPosition($player);
        } else {
            return $this->getNbLostPosition($player);
        }
    }

    /**
     * @param $player
     * @return mixed
     */
    public function getGameStats($player)
    {
        $playerGames = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerGame')->getFromPlayer($player);
        $stats = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->getStatsFromPlayer($player);

        foreach ($playerGames as $playerGame) {
            if (isset($stats[$playerGame->getGame()->getId()])) {
                $playerGame->setStatuses(
                    $stats[$playerGame->getGame()
                        ->getId()]
                );
            }
        }
        return $playerGames;
    }

    /**
     *
     */
    public function majRulesOfThree()
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
