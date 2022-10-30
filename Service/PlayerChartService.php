<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartStatusRepository;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\PlayerGameRankingUpdater;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\PlayerGroupRankingUpdater;
use VideoGamesRecords\CoreBundle\Service\Ranking\Updater\PlayerRankingUpdater;

class PlayerChartService
{
    private PlayerGameRankingUpdater $playerGameRankingUpdater;
    private PlayerGroupRankingUpdater $playerGroupRankingUpdater;
    private PlayerRankingUpdater $playerRankingUpdate;
    private GameService $gameService;
    private ChartService $chartService;
    private PlayerChartRepository $playerChartRepository;
    private PlayerChartStatusRepository $playerChartStatusRepository;

    public function __construct(
        PlayerGameRankingUpdater $playerGameRankingUpdater,
        PlayerGroupRankingUpdater $playerGroupRankingUpdater,
        PlayerRankingUpdater $playerRankingUpdate,
        GameService $gameService,
        ChartService $chartService,
        PlayerChartRepository $playerChartRepository,
        PlayerChartStatusRepository $playerChartStatusRepository
    ) {
        $this->playerGameRankingUpdater = $playerGameRankingUpdater;
        $this->playerGroupRankingUpdater = $playerGroupRankingUpdater;
        $this->playerRankingUpdate = $playerRankingUpdate;
        $this->gameService = $gameService;
        $this->chartService = $chartService;
        $this->playerChartRepository = $playerChartRepository;
        $this->playerChartStatusRepository = $playerChartStatusRepository;
    }


    /**
     * @param int $nbChartToMaj
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ExceptionInterface
     * @throws Exception
     */
    public function majRanking(int $nbChartToMaj = 100): int
    {
        $this->chartService->goToMajPlayer($nbChartToMaj);
        $charts = $this->chartService->getChartToMajPlayer();

        $playerList = [];
        $groupList  = [];
        $gameList   = [];

        foreach ($charts as $chart) {
            $idGroup = $chart->getGroup()->getId();
            $idGame  = $chart->getGroup()->getGame()->getId();
            //----- Player
            $playerList = array_merge($playerList, $this->playerChartRepository->maj($chart));
            //----- Group
            if (!isset($groupList[$idGroup])) {
                $groupList[$idGroup] = $chart->getGroup();
            }
            //----- Game
            if (!isset($gameList[$idGame])) {
                $gameList[$idGame] = $chart->getGroup()->getGame();
            }
        }

        //----- Maj group
        foreach ($groupList as $group) {
            $this->playerGroupRankingUpdater->maj($group->getId());
        }

        //----- Maj game
        foreach ($gameList as $game) {
            $this->playerGameRankingUpdater->maj($game->getId());
        }

        //----- Maj player
        foreach ($playerList as $player) {
            $this->playerRankingUpdate->maj($player->getId());
            if ($player->getCountry()) {
                $player->getCountry()->setBoolMaj(true);
            }
        }

        $this->playerChartRepository->flush();
        $this->chartService->goToNormalPlayer();
        return count($charts);
    }

    /**
     * @param $player
     * @param $game
     * @param $platform
     */
    public function majPlatform($player, $game, $platform) {
        // Update platform
        $this->playerChartRepository->majPlatform(
            $player,
            $game,
            $platform
        );
        // Maj all charts ot game
        $this->gameService->majChartStatus($game->getId(), 'MAJ');
    }

    /**
     * @throws ORMException
     */
    public function majInvestigation()
    {
        $list = $this->playerChartRepository->getPlayerChartToDesactivate();
        $statut = $this->playerChartStatusRepository->getReference(PlayerChartStatus::ID_STATUS_NOT_PROOVED);
        /** @var PlayerChart $playerChart */
        foreach ($list as $playerChart) {
            $playerChart->setStatus($statut);
            $this->playerChartRepository->flush();
        }
    }
}
