<?php

namespace VideoGamesRecords\CoreBundle\Service\Badge;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\TeamGameRanking;

class TeamMasterBadge
{
    private EntityManagerInterface $em;
    private TeamGameRanking $teamGameRanking;

    public function __construct(EntityManagerInterface $em, TeamGameRanking $teamGameRanking)
    {
        $this->em = $em;
        $this->teamGameRanking = $teamGameRanking;
    }

    public function maj(Game $game): void
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->teamGameRanking->getRankingPoints($game->getId(), ['maxRank' => 1]);
        $teams = array();
        foreach ($ranking as $teamGame) {
            $teams[$teamGame->getTeam()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamBadge')->updateBadge($teams, $game->getBadge());
    }
}
