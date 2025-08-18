<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Message\Dispatcher;

use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerChartRank;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerData;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerRank;
use VideoGamesRecords\CoreBundle\Message\Team\UpdateTeamChartRank;

readonly class RankingUpdateDispatcher
{
    public function __construct(
        private MessageBusInterface $bus,
    ) {
    }

    /**
     * @param Game $game
     * @throws ExceptionInterface
     */
    public function updatePlayerRankFromGame(Game $game): void
    {
        foreach ($game->getGroups() as $group) {
            $this->updatePlayerRankFromGroup($group);
        }
    }

    /**
     * @param Group $group
     * @throws ExceptionInterface
     */
    public function updatePlayerRankFromGroup(Group $group): void
    {
        foreach ($group->getCharts() as $chart) {
            $this->bus->dispatch(new UpdatePlayerChartRank($chart->getId()));
        }
    }

    /**
     * @param Player $player
     * @throws ExceptionInterface
     */
    public function updateTeamRankFromPlayer(Player $player): void
    {
        /** @var PlayerChart $playerChart */
        foreach ($player->getPlayerCharts() as $playerChart) {
            $this->bus->dispatch(new UpdateTeamChartRank($playerChart->getChart()->getId()));
        }
    }

    /**
     * @param Player $player
     * @throws ExceptionInterface
     */
    public function updatePlayerRankFromPlayer(Player $player): void
    {
        $this->bus->dispatch(new UpdatePlayerData($player->getId()));
        $this->bus->dispatch(new UpdatePlayerRank());
    }
}
