<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Updater;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Team;

class ScoringTeamRankingUpdater
{
    private EntityManagerInterface $em;
    private TeamChartRankingUpdater $teamChartRankingUpdater;
    private TeamGroupRankingUpdater $teamGroupRankingUpdater;
    private TeamGameRankingUpdater $teamGameRankingUpdater;
    private TeamRankingUpdater $teamRankingUpdater;

    public function __construct(
        EntityManagerInterface $em,
        TeamChartRankingUpdater $teamChartRankingUpdater,
        TeamGroupRankingUpdater $teamGroupRankingUpdater,
        TeamGameRankingUpdater $teamGameRankingUpdater,
        TeamRankingUpdater $teamRankingUpdater
    ) {
        $this->em = $em;
        $this->teamChartRankingUpdater = $teamChartRankingUpdater;
        $this->teamGroupRankingUpdater = $teamGroupRankingUpdater;
        $this->teamGameRankingUpdater = $teamGameRankingUpdater;
        $this->teamRankingUpdater = $teamRankingUpdater;
    }

    /**
     * @return int
     */
    public function process(): int
    {
        $charts = $this->getChartsToUpdate();

        /** @var Chart $chart */
        foreach ($charts as $chart) {
            $this->teamChartRankingUpdater->maj($chart->getId());
            $chart->setStatusTeam(Chart::STATUS_NORMAL);
        }

        $groups = $this->teamChartRankingUpdater->getGroups();
        $games = $this->teamChartRankingUpdater->getGames();
        $teams = $this->teamChartRankingUpdater->getTeams();

        //----- Maj group
        foreach ($groups as $group) {
            $this->teamGroupRankingUpdater->maj($group->getId());
        }

        //----- Maj game
        foreach ($games as $game) {
            $this->teamGameRankingUpdater->maj($game->getId());
        }

        /** @var Team $team */
        foreach ($teams as $team) {
            $this->teamRankingUpdater->maj($team->getId());
        }

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
            ->setParameter('status', Chart::STATUS_MAJ)
            ->setMaxResults(100);

        return $query->getQuery()->getResult();
    }
}
