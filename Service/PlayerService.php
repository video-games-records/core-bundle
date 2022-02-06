<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Repository\LostPositionRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class PlayerService
{
    private EntityManagerInterface $em;
    private PlayerRepository $playerRepository;
    private LostPositionRepository $lostPositionRepository;

    public function __construct(EntityManagerInterface $em, PlayerRepository $playerRepository, LostPositionRepository $lostPositionRepository)
    {
        $this->em = $em;
        $this->playerRepository = $playerRepository;
        $this->lostPositionRepository = $lostPositionRepository;
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
        $playerGames = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getFromPlayer($player);
        $stats = $this->em->getRepository('VideoGamesRecordsCoreBundle:Game')->getStatsFromPlayer($player);

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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function maj()
    {
        $players = $this->playerRepository->findBy(['boolMaj' => true]);
        foreach ($players as $player) {
            $this->playerRepository->maj($player);
            if ($player->getCountry()) {
                $player->getCountry()->setBoolMaj(true);
            }
        }
        $this->playerRepository->majGameRank();
        $this->playerRepository->majRankPointChart();
        $this->playerRepository->majRankPointGame();
        $this->playerRepository->majRankMedal();
        $this->playerRepository->majRankCup();
        $this->playerRepository->majRankProof();
    }


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function majRankBadge()
    {
        $this->playerRepository->majPointBadge();
        $this->playerRepository->majRankBadge();
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
