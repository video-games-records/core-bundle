<?php

namespace VideoGamesRecords\CoreBundle\Service\Badge;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerGameRankingSelect;

class PlayerMasterBadge
{
    private EntityManagerInterface $em;
    private PlayerGameRankingSelect $playerGameRankingSelect;

    public function __construct(EntityManagerInterface $em, PlayerGameRankingSelect $playerGameRankingSelect)
    {
        $this->em = $em;
        $this->playerGameRankingSelect = $playerGameRankingSelect;
    }

    public function maj(Game $game): void
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->playerGameRankingSelect->getRankingPoints($game->getId(), ['maxRank' => 1]);
        $players = array();
        foreach ($ranking as $playerGame) {
            $players[$playerGame->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $game->getBadge());
    }
}
