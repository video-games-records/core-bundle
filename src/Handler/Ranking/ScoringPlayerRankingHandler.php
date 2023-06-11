<?php

namespace VideoGamesRecords\CoreBundle\Handler\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Handler\Ranking\Player\PlayerChartRankingHandler;
use VideoGamesRecords\CoreBundle\Handler\Ranking\Player\PlayerGameRankingHandler;
use VideoGamesRecords\CoreBundle\Handler\Ranking\Player\PlayerGroupRankingHandler;
use VideoGamesRecords\CoreBundle\Handler\Ranking\Player\PlayerRankingHandler;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;

class ScoringPlayerRankingHandler
{
    private EntityManagerInterface $em;
    private PlayerChartRankingHandler $playerChartRankingHandler;
    private PlayerGroupRankingHandler $playerGroupRankingHandler;
    private PlayerGameRankingHandler $playerGameRankingHandler;
    private PlayerRankingHandler $playerRankingHandler;

    public function __construct(
        EntityManagerInterface $em,
        PlayerChartRankingHandler $playerChartRankingHandler,
        PlayerGroupRankingHandler $playerGroupRankingHandler,
        PlayerGameRankingHandler$playerGameRankingHandler,
        PlayerRankingHandler $playerRankingHandler
    ) {
        $this->em = $em;
        $this->playerChartRankingHandler = $playerChartRankingHandler;
        $this->playerGroupRankingHandler = $playerGroupRankingHandler;
        $this->playerGameRankingHandler = $playerGameRankingHandler;
        $this->playerRankingHandler = $playerRankingHandler;
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     */
    public function handle(): int
    {
        $charts = $this->getChartsToUpdate();

        /** @var Chart $chart */
        foreach ($charts as $chart) {
            $this->playerChartRankingHandler->handle($chart->getId());
            $chart->setStatusPlayer(ChartStatus::STATUS_NORMAL);
        }

        $groups = $this->playerChartRankingHandler->getGroups();
        $games = $this->playerChartRankingHandler->getGames();
        $players = $this->playerChartRankingHandler->getPlayers();

        //----- Maj group
        foreach ($groups as $group) {
            $this->playerGroupRankingHandler->handle($group->getId());
        }

        //----- Maj game
        foreach ($games as $game) {
            $this->playerGameRankingHandler->handle($game->getId());
        }

        foreach ($players as $player) {
            $this->playerRankingHandler->handle($player->getId());
        }

        $this->playerRankingHandler->majRank();

        echo sprintf("%d charts updated\n", count($charts));
        echo sprintf("%d groups updated\n", count($groups));
        echo sprintf("%d games updated\n", count($games));
        echo sprintf("%d players updated\n", count($players));
        return 0;
    }


    private function getChartsToUpdate()
    {
        $query = $this->em->createQueryBuilder()
            ->select('ch')
            ->from('VideoGamesRecords\CoreBundle\Entity\Chart', 'ch')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->where('ch.statusPlayer = :status')
            ->setParameter('status', ChartStatus::STATUS_MAJ)
            ->setMaxResults(100);

        return $query->getQuery()->getResult();
    }
}
