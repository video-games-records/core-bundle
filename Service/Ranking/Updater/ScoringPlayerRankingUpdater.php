<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Updater;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Player;

class ScoringPlayerRankingUpdater
{
    private EntityManagerInterface $em;
    private PlayerChartRankingUpdater $playerChartRankingUpdater;
    private PlayerGroupRankingUpdater $playerGroupRankingUpdater;
    private PlayerGameRankingUpdater $playerGameRankingUpdater;
    private PlayerRankingUpdater $playerRankingUpdater;

    public function __construct(
        EntityManagerInterface $em,
        PlayerChartRankingUpdater $playerChartRankingUpdater,
        PlayerGroupRankingUpdater $playerGroupRankingUpdater,
        PlayerGameRankingUpdater $playerGameRankingUpdater,
        PlayerRankingUpdater $playerRankingUpdater
    ) {
        $this->em = $em;
        $this->playerChartRankingUpdater = $playerChartRankingUpdater;
        $this->playerGroupRankingUpdater = $playerGroupRankingUpdater;
        $this->playerGameRankingUpdater = $playerGameRankingUpdater;
        $this->playerRankingUpdater = $playerRankingUpdater;
    }

    /**
     * @return int
     */
    public function process(): int
    {
        $charts = $this->getChartsToUpdate();

        /** @var Chart $chart */
        foreach ($charts as $chart) {
            $this->playerChartRankingUpdater->maj($chart->getId());
            $chart->setStatusPlayer(Chart::STATUS_NORMAL);
        }

        $groups = $this->playerChartRankingUpdater->getGroups();
        $games = $this->playerChartRankingUpdater->getGames();
        $players = $this->playerChartRankingUpdater->getPlayers();

        //----- Maj group
        foreach ($groups as $group) {
            $this->playerGroupRankingUpdater->maj($group->getId());
        }

        //----- Maj game
        foreach ($games as $game) {
            $this->playerGameRankingUpdater->maj($game->getId());
        }

        /** @var Player $player */
        foreach ($players as $player) {
            $this->playerRankingUpdater->maj($player->getId());
            /*if ($player->getCountry()) {
                $player->getCountry()->setBoolMaj(true);
            }*/
        }

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
            ->setParameter('status', Chart::STATUS_MAJ)
            ->setMaxResults(100);

        return $query->getQuery()->getResult();
    }
}
