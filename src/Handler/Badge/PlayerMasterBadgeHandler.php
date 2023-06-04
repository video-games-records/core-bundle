<?php

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlayerGameRankingQuery;

class PlayerMasterBadgeHandler
{
    private EntityManagerInterface $em;
    private PlayerGameRankingQuery $playerGameRankingQuery;

    public function __construct(EntityManagerInterface $em, PlayerGameRankingQuery $playerGameRankingQuery)
    {
        $this->em = $em;
        $this->playerGameRankingQuery = $playerGameRankingQuery;
    }

    /**
     * @throws ORMException
     */
    public function process(Game $game): void
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->playerGameRankingQuery->getRankingPoints($game->getId(), ['maxRank' => 1]);
        $players = array();
        foreach ($ranking as $playerGame) {
            $players[$playerGame->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $game->getBadge());
    }
}
