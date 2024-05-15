<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Player\LostPosition;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Manager\LostPositionManager;

class GetNbLostPosition extends AbstractController
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
    public function __invoke(Player $player): int
    {
        return $this->lostPositionManager->getNbLostPosition($player);
    }
}
