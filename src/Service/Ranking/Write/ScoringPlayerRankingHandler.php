<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Write;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Player;
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

        /** @var Player $player */
        foreach ($players as $player) {
            $this->playerRankingHandler->handle($player->getId());
        }

        $this->playerRankingHandler->majRank();

        $this->em->flush();
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
            ->andWhere('ch.statusPlayer = :status')
            ->setParameter('status', ChartStatus::STATUS_MAJ)
            ->setMaxResults(100);

        return $query->getQuery()->getResult();
    }
}
