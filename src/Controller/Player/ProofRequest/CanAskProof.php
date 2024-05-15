<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Player\ProofRequest;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\DataProvider\CanAskProofProvider;
use VideoGamesRecords\CoreBundle\Entity\Player;

class CanAskProof extends AbstractController
{
    private CanAskProofProvider $canAskProofProvider;

    public function __construct(CanAskProofProvider $canAskProofProvider)
    {
        $this->canAskProofProvider = $canAskProofProvider;
    }

    /**
     * @param Player $player
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function __invoke(Player $player): bool
    {
        return $this->canAskProofProvider->load($player);
    }
}
