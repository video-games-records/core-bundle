<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Service\Player\CanAskProofProvider;

/**
 * Class ProofRequestController
 */
class ProofRequestController extends AbstractController
{
    private CanAskProofProvider $canAskProofProvider;

    public function __construct(CanAskProofProvider $canAskProofProvider)
    {
        $this->canAskProofProvider = $canAskProofProvider;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canAskProof(Player $player): bool
    {
        return $this->canAskProofProvider->load($player);
    }
}
