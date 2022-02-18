<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\PlayerGame;
use VideoGamesRecords\CoreBundle\Entity\PlayerGroup;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerGameRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerGroupRepository;

class PlayerChartService
{
    private GameService $gameService;
    private GroupService $groupService;
    private ChartService $chartService;
    private PlayerService $playerService;
    private PlayerChartRepository $playerChartRepository;
    private PlayerGroupRepository $playerGroupRepository;
    private PlayerGameRepository $playerGameRepository;

    public function __construct(
        GameService $gameService,
        GroupService $groupService,
        ChartService $chartService,
        PlayerService $playerService,
        PlayerChartRepository $playerChartRepository,
        PlayerGroupRepository $playerGroupRepository,
        PlayerGameRepository $playerGameRepository
    ) {
        $this->gameService = $gameService;
        $this->groupService = $groupService;
        $this->chartService = $chartService;
        $this->playerService = $playerService;
        $this->playerChartRepository = $playerChartRepository;
        $this->playerGroupRepository = $playerGroupRepository;
        $this->playerGameRepository = $playerGameRepository;
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
            $this->groupService->majPlayerGroup($group->getId());
        }

        //----- Maj game
        foreach ($gameList as $game) {
            $this->gameService->majPlayerGame($game->getId());
            $this->gameService->majPlayerMasterBadge($game->getId());
        }

        //----- Maj player
        foreach ($playerList as $player) {
            $this->playerService->majPlayer($player);
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
        $statusReference = $this->em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NOT_PROOVED);
        /** @var PlayerChart $playerChart */
        foreach ($list as $playerChart) {
            $playerChart->setStatus($statusReference);
            $this->playerChartRepository->flush();
        }
    }


    /**
     * @param PlayerChart $playerChart
     * @throws ORMException
     */
    public function incrementNbChartProven(PlayerChart $playerChart)
    {
        $this->updateNbChartProven($playerChart, 1);
    }

    /**
     * @param PlayerChart $playerChart
     * @throws ORMException
     */
    public function decrementNbChartProven(PlayerChart $playerChart)
    {
        $this->updateNbChartProven($playerChart, -1);
    }

    /**
     * @param PlayerChart $playerChart
     * @param int         $nb
     * @throws ORMException
     */
    private function updateNbChartProven(PlayerChart $playerChart, int $nb)
    {
        /** @var PlayerGroup $playerGroup */
        $playerGroup = $this->playerGroupRepository->findOneBy(
            array(
                'player' => $playerChart->getPlayer(),
                'group' => $playerChart->getChart()->getGroup()
            )
        );
        if ($playerGroup) {
            $playerGroup->setNbChartProven($playerGroup->getNbChartProven() + $nb);
        }

        /** @var PlayerGame $playerGame */
        $playerGame = $this->playerGameRepository->findOneBy(
            array(
                'player' => $playerChart->getPlayer(),
                'game' => $playerChart->getChart()->getGroup()->getGame()
            )
        );
        if ($playerGame) {
            $playerGame->setNbChartProven($playerGame->getNbChartProven() + $nb);
        }

        $playerChart->getPlayer()->setNbChartProven($playerChart->getPlayer()->getNbChartProven() + $nb);
        $this->playerChartRepository->flush();
    }
}
