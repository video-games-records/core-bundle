<?php

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\TeamGameRankingQuery;

class TeamMasterBadgeHandler
{
    private EntityManagerInterface $em;
    private TeamGameRankingQuery $teamGameRankingQuery;

    public function __construct(EntityManagerInterface $em, TeamGameRankingQuery $teamGameRankingQuery)
    {
        $this->em = $em;
        $this->teamGameRankingQuery = $teamGameRankingQuery;
    }

    /**
     * @throws ORMException
     */
    public function process(Game $game): void
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->teamGameRankingQuery->getRankingPoints($game->getId(), ['maxRank' => 1]);
        $teams = array();
        foreach ($ranking as $teamGame) {
            $teams[$teamGame->getTeam()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamBadge')->updateBadge($teams, $game->getBadge());
    }
}
