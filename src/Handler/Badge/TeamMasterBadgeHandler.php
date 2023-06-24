<?php

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;

class TeamMasterBadgeHandler
{
    private EntityManagerInterface $em;
    private RankingProviderInterface $rankingProvider; //TeamGameRankingQuery

    public function __construct(EntityManagerInterface $em, RankingProviderInterface $rankingProvider)
    {
        $this->em = $em;
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @throws ORMException
     */
    public function process(Game $game): void
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->rankingProvider->getRankingPoints($game->getId(), ['maxRank' => 1]);
        $teams = array();
        foreach ($ranking as $teamGame) {
            $teams[$teamGame->getTeam()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamBadge')->updateBadge($teams, $game->getBadge());
    }
}
