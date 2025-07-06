<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\MessageHandler\Player;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerRank;
use VideoGamesRecords\CoreBundle\Ranking\Command\RankUpdate\PlayerRankUpdateHandler;

#[AsMessageHandler]
readonly class UpdatePlayerRankHandler
{
    public function __construct(
        private PlayerRankUpdateHandler $rankUpdateHandler,
    ) {
    }

    /**
     */
    public function __invoke(UpdatePlayerRank $updatePlayerChartRank): void
    {
        $this->rankUpdateHandler->majRankPointChart();
        $this->rankUpdateHandler->majRankPointGame();
        $this->rankUpdateHandler->majRankCup();
        $this->rankUpdateHandler->majRankMedal();
        $this->rankUpdateHandler->majRankBadge();
        $this->rankUpdateHandler->majRankProof();
    }
}
