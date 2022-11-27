<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Write;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;

class ScoringTeamRankingHandler
{
    private EntityManagerInterface $em;
    private TeamChartRankingHandler $teamChartRankingHandler;
    private TeamGroupRankingHandler $teamGroupRankingHandler;
    private TeamGameRankingHandler $teamGameRankingHandler;
    private TeamRankingHandler $teamRankingHandler;

    public function __construct(
        EntityManagerInterface $em,
        TeamChartRankingHandler $teamChartRankingHandler,
        TeamGroupRankingHandler $teamGroupRankingHandler,
        TeamGameRankingHandler $teamGameRankingHandler,
        TeamRankingHandler $teamRankingHandler
    ) {
        $this->em = $em;
        $this->teamChartRankingHandler = $teamChartRankingHandler;
        $this->teamGroupRankingHandler = $teamGroupRankingHandler;
        $this->teamGameRankingHandler = $teamGameRankingHandler;
        $this->teamRankingHandler = $teamRankingHandler;
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
            $chart->setStatusTeam(ChartStatus::STATUS_NORMAL);
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

        $this->teamRankingHandler->majRank();

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
            ->andWhere('ch.statusTeam = :status')
            ->setParameter('status', ChartStatus::STATUS_MAJ)
            ->setMaxResults(100);

        return $query->getQuery()->getResult();
    }
}
