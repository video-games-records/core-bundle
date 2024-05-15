<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerChartRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerGameRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerGroupRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerSerieRankingHandler;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class ScoringPlayerRankingHandler
{
    private EntityManagerInterface $em;
    private PlayerChartRankingHandler $playerChartRankingHandler;
    private PlayerGroupRankingHandler $playerGroupRankingHandler;
    private PlayerGameRankingHandler $playerGameRankingHandler;
    private PlayerSerieRankingHandler $playerSerieRankingHandler;
    private PlayerRankingHandler $playerRankingHandler;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $em,
        PlayerChartRankingHandler $playerChartRankingHandler,
        PlayerGroupRankingHandler $playerGroupRankingHandler,
        PlayerGameRankingHandler $playerGameRankingHandler,
        PlayerSerieRankingHandler $playerSerieRankingHandler,
        PlayerRankingHandler $playerRankingHandler,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->playerChartRankingHandler = $playerChartRankingHandler;
        $this->playerGroupRankingHandler = $playerGroupRankingHandler;
        $this->playerGameRankingHandler = $playerGameRankingHandler;
        $this->playerSerieRankingHandler = $playerSerieRankingHandler;
        $this->playerRankingHandler = $playerRankingHandler;
        $this->eventDispatcher = $eventDispatcher;
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
            $chart->setStatusPlayer(ChartStatus::NORMAL);
        }

        $groups = $this->playerChartRankingHandler->getGroups();
        $games = $this->playerChartRankingHandler->getGames();
        $players = $this->playerChartRankingHandler->getPlayers();
        $series = $this->playerChartRankingHandler->getSeries();

        //----- Maj group
        foreach ($groups as $group) {
            $this->playerGroupRankingHandler->handle($group->getId());
        }

        //----- Maj game
        foreach ($games as $game) {
            $this->playerGameRankingHandler->handle($game->getId());
        }

        //----- Maj serie
        foreach ($series as $serie) {
            $this->playerSerieRankingHandler->handle($serie->getId());
        }

        foreach ($players as $player) {
            $this->playerRankingHandler->handle($player->getId());
        }

        $event = new Event();
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::SCORES_PLAYER_MAJ_COMPLETED);
        //$this->playerRankingHandler->majRank();

        echo sprintf("%d charts updated\n", count($charts));
        echo sprintf("%d groups updated\n", count($groups));
        echo sprintf("%d games updated\n", count($games));
        echo sprintf("%d series updated\n", count($series));
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
            ->setParameter('status', ChartStatus::MAJ)
            ->setMaxResults(100);

        return $query->getQuery()->getResult();
    }
}
