<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Ranking\Command\Team\TeamChartRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Team\TeamGameRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Team\TeamGroupRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Team\TeamRankingHandler;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class ScoringTeamRankingHandler
{
    private EntityManagerInterface $em;
    private TeamChartRankingHandler $teamChartRankingHandler;
    private TeamGroupRankingHandler $teamGroupRankingHandler;
    private TeamGameRankingHandler $teamGameRankingHandler;
    private TeamRankingHandler $teamRankingHandler;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $em,
        TeamChartRankingHandler $teamChartRankingHandler,
        TeamGroupRankingHandler $teamGroupRankingHandler,
        TeamGameRankingHandler $teamGameRankingHandler,
        TeamRankingHandler $teamRankingHandler,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->teamChartRankingHandler = $teamChartRankingHandler;
        $this->teamGroupRankingHandler = $teamGroupRankingHandler;
        $this->teamGameRankingHandler = $teamGameRankingHandler;
        $this->teamRankingHandler = $teamRankingHandler;
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
            $this->teamChartRankingHandler->handle($chart->getId());
            $chart->setStatusTeam(ChartStatus::NORMAL);
        }

        $groups = $this->teamChartRankingHandler->getGroups();
        $games = $this->teamChartRankingHandler->getGames();
        $teams = $this->teamChartRankingHandler->getTeams();

        //----- Maj group
        foreach ($groups as $group) {
            $this->teamGroupRankingHandler->handle($group->getId());
        }

        //----- Maj game
        foreach ($games as $game) {
            $this->teamGameRankingHandler->handle($game->getId());
        }

        /** @var Team $team */
        foreach ($teams as $team) {
            $this->teamRankingHandler->handle($team->getId());
        }

        $event = new Event();
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::SCORES_TEAM_MAJ_COMPLETED);
        //$this->teamRankingHandler->majRank();

        $this->em->flush();
        echo sprintf("%d charts updated\n", count($charts));
        echo sprintf("%d groups updated\n", count($groups));
        echo sprintf("%d games updated\n", count($games));
        echo sprintf("%d teams updated\n", count($teams));
        return 0;
    }


    private function getChartsToUpdate()
    {
        $query = $this->em->createQueryBuilder()
            ->select('ch')
            ->from('VideoGamesRecords\CoreBundle\Entity\Chart', 'ch')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->where('ch.statusPlayer = :statusPlayer')
            ->setParameter('statusPlayer', ChartStatus::NORMAL)
            ->andWhere('ch.statusTeam = :statusTeam')
            ->setParameter('statusTeam', ChartStatus::MAJ)
            ->setMaxResults(100);

        return $query->getQuery()->getResult();
    }
}
