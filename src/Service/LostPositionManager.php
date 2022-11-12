<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Repository\LostPositionRepository;

class LostPositionManager
{
    private LostPositionRepository $lostPositionRepository;


    public function __construct(LostPositionRepository $lostPositionRepository)
    {
        $this->lostPositionRepository = $lostPositionRepository;
    }

    /**
     * @param Player $player
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNbLostPosition(Player $player): int
    {
        return $this->lostPositionRepository->getNbLostPosition($player);
    }

    /**
     * @param Player $player
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNbNewLostPosition(Player $player): int
    {
        if ($player->getLastDisplayLostPosition() != null) {
            return $this->lostPositionRepository->getNbNewLostPosition($player);
        } else {
            return $this->getNbLostPosition($player);
        }
    }
}
