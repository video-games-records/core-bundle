<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class PlayerService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @param $player
     * @return mixed
     */
    public function getNbLostPosition($player)
    {
        return $this->em->getRepository('VideoGamesRecordsCoreBundle:LostPosition')->getNbLostPosition($player);
    }

    /**
     * @param $player
     * @return mixed
     */
    public function getNbNewLostPosition($player)
    {
        if ($player->getLastDisplayLostPosition() != null) {
            return $this->em->getRepository('VideoGamesRecordsCoreBundle:LostPosition')
                ->getNbNewLostPosition($player);
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
        /** @var PlayerRepository $playerRepository */
        $playerRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Player');
        $players = $playerRepository->findBy(['boolMaj' => true]);
        foreach ($players as $player) {
            $playerRepository->maj($player);
        }
        $playerRepository->majGameRank();
        $playerRepository->majRankPointChart();
        $playerRepository->majRankPointGame();
        $playerRepository->majRankMedal();
        $playerRepository->majRankCup();
        $playerRepository->majRankProof();
    }


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function majRankBadge()
    {
        /** @var PlayerRepository $playerRepository */
        $playerRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Player');
        $playerRepository->majPointBadge();
        $playerRepository->majRankBadge();
    }


    /**
     * @throws ORMException
     */
    public function majRulesOfThree()
    {
        /** @var PlayerRepository $playerRepository */
        $playerRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Player');

        $group1 = $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\User\GroupInterface', 2);
        $group2 = $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\User\GroupInterface', 9);

        $players = $playerRepository->getPlayerToDisabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->removeGroup($group1);
            $user->addGroup($group2);
        }
        $this->em->flush();

        $players = $playerRepository->getPlayerToEnabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->addGroup($group1);
            $user->removeGroup($group2);
        }
        $this->em->flush();
    }
}
