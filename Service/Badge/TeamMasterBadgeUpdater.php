<?php

namespace VideoGamesRecords\CoreBundle\Service\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Select\TeamGameRankingSelect;

class TeamMasterBadgeUpdater
{
    private EntityManagerInterface $em;
    private TeamGameRankingSelect $teamGameRankingSelect;

    public function __construct(EntityManagerInterface $em, TeamGameRankingSelect $teamGameRankingSelect)
    {
        $this->em = $em;
        $this->teamGameRankingSelect = $teamGameRankingSelect;
    }

    /**
     * @throws ORMException
     */
    public function process(Game $game): void
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->teamGameRankingSelect->getRankingPoints($game->getId(), ['maxRank' => 1]);
        $teams = array();
        foreach ($ranking as $teamGame) {
            $teams[$teamGame->getTeam()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamBadge')->updateBadge($teams, $game->getBadge());
    }
}
