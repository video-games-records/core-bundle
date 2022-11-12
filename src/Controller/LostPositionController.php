<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Service\LostPositionManager;

/**
 * Class LostPositionController
 */
class LostPositionController extends AbstractController
{
    private LostPositionManager $lostPositionManager;

    public function __construct(LostPositionManager $lostPositionManager)
    {
        $this->lostPositionManager = $lostPositionManager;
    }


    /**
     * @param Player $player
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getPlayerNbLostPosition(Player $player): int
    {
        return $this->lostPositionManager->getNbLostPosition($player);
    }

    /**
     * @param Player $player
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getPlayerNbNewLostPosition(Player $player): int
    {
        return $this->lostPositionManager->getNbNewLostPosition($player);
    }
}
