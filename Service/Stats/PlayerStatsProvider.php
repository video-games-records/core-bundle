<?php

namespace VideoGamesRecords\CoreBundle\Service\Stats;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;
use VideoGamesRecords\CoreBundle\Repository\ProofRequestRepository;

class PlayerStatsProvider implements StatsProviderInterface
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
    )
    {
        $this->em = $em;
    }


    /**
     * @param $mixed
     * @return array
     */
    public function load($mixed): array
    {
        $playerGames = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerGame')
            ->getFromPlayer($mixed);
        $stats = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')
            ->getStatsFromPlayer($mixed);

        foreach ($playerGames as $playerGame) {
            if (isset(
                $stats[$playerGame->getGame()
                    ->getId()]
            )) {
                $playerGame->setStatuses(
                    $stats[$playerGame->getGame()
                        ->getId()]
                );
            }
        }
        return $playerGames;
    }
}

