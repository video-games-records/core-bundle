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
use VideoGamesRecords\CoreBundle\Service\Ranking\Write\PlayerGameRankingHandler;
use VideoGamesRecords\CoreBundle\Service\Ranking\Write\PlayerGroupRankingHandler;
use VideoGamesRecords\CoreBundle\Service\Ranking\Write\PlayerRankingHandler;

class PlayerChartService
{
    private GameService $gameService;
    private PlayerChartRepository $playerChartRepository;
    private PlayerChartStatusRepository $playerChartStatusRepository;

    public function __construct(
        GameService $gameService,
        PlayerChartRepository $playerChartRepository,
        PlayerChartStatusRepository $playerChartStatusRepository
    ) {
        $this->gameService = $gameService;
        $this->playerChartRepository = $playerChartRepository;
        $this->playerChartStatusRepository = $playerChartStatusRepository;
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
