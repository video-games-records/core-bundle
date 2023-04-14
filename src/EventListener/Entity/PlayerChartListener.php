<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Service\Stats\Write\GameStatsHandler;
use VideoGamesRecords\CoreBundle\Service\Stats\Write\GroupStatsHandler;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;

class PlayerChartListener
{
    private array $changeSet = array();

    public function __construct(
        private readonly GameStatsHandler $gameStatsHandler,
        private readonly GroupStatsHandler $groupStatsHandler
    ) {}

    /**
     * @param PlayerChart        $playerChart
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function prePersist(PlayerChart $playerChart, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $playerChart->setStatus($em->getReference('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus', 1));
        $playerChart->setLastUpdate(new DateTime());

        // Chart
        $chart = $playerChart->getChart();
        $chart->setNbPost($chart->getNbPost() + 1);
        $chart->setStatusPlayer(ChartStatus::STATUS_MAJ);
        $chart->setStatusTeam(ChartStatus::STATUS_MAJ);

        // Group
        $group = $chart->getGroup();
        $group->setNbPost($group->getNbPost() + 1);

        // Game
        $game = $group->getGame();
        $game->setNbPost($game->getNbPost() + 1);

        // Player
        $player = $playerChart->getPlayer();
        $player->setNbChart($player->getNbChart() + 1);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function postPersist(PlayerChart $playerChart, LifecycleEventArgs $event): void
    {
        //$this->groupStatsHandler->majNbPlayer($playerChart->getChart()->getGroup());
        //$this->gameStatsHandler->majNbPlayer($playerChart->getChart()->getGroup()->getGame());
    }

    /**
     * @param PlayerChart        $playerChart
     * @param PreUpdateEventArgs $event
     * @throws ORMException
     */
    public function preUpdate(PlayerChart $playerChart, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
        $em = $event->getObjectManager();

        // Update by player
        if (array_key_exists('lastUpdate', $this->changeSet)) {
            $playerChart->setStatus($em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL));
        }

        if (array_key_exists('platform', $this->changeSet)) {
            $chart = $playerChart->getChart();
            $chart->setStatusPlayer(ChartStatus::STATUS_MAJ);
        }

        $playerChart->setTopScore(false);
        if ($playerChart->getRank() === 1) {
            $playerChart->setTopScore(true);
        }

        if ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL) {
            $playerChart->setProof(null);
        }

        if (null === $playerChart->getDateInvestigation() && PlayerChartStatus::ID_STATUS_INVESTIGATION === $playerChart->getStatus()->getId()) {
            $playerChart->setDateInvestigation(new DateTime());
        }
        if (null !== $playerChart->getDateInvestigation()
            && in_array($playerChart->getStatus()->getId(), [PlayerChartStatus::ID_STATUS_PROOVED, PlayerChartStatus::ID_STATUS_NOT_PROOVED], true)) {
            $playerChart->setDateInvestigation(null);
        }

        //-- status
        if ($playerChart->getStatus()->getId() == PlayerChartStatus::ID_STATUS_NOT_PROOVED) {
            $playerChart->setPointChart(0);
            $playerChart->setRank(0);
            $playerChart->setTopScore(false);
        }
    }

    /**
     * @param PlayerChart              $playerChart
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postUpdate(PlayerChart $playerChart, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();

        if ((array_key_exists('lastUpdate', $this->changeSet)) || (array_key_exists('status', $this->changeSet))) {
            $chart = $playerChart->getChart();
            $chart->setStatusPlayer(ChartStatus::STATUS_MAJ);
            $chart->setStatusTeam(ChartStatus::STATUS_MAJ);
        }

        if (array_key_exists('status', $this->changeSet)) {
            $player = $playerChart->getPlayer();

            if ($this->changeSet['status'][1]->getId() == PlayerChartStatus::ID_STATUS_PROOVED) {
                $player->setNbChartProven($player->getNbChartProven() + 1);
            }

            if ($this->changeSet['status'][0]->getId() == PlayerChartStatus::ID_STATUS_PROOVED) {
                $player->setNbChartProven($player->getNbChartProven() - 1);
            }

            if ($this->changeSet['status'][1]->getId() == PlayerChartStatus::ID_STATUS_NOT_PROOVED) {
                $player->setNbChartDisabled($player->getNbChartDisabled() + 1);
            }

            if ($this->changeSet['status'][0]->getId() == PlayerChartStatus::ID_STATUS_NOT_PROOVED) {
                $player->setNbChartDisabled($player->getNbChartDisabled() - 1);
            }
        }

        $em->flush();
    }

    /**
     * @param PlayerChart            $playerChart
     * @param LifecycleEventArgs $event
     */
    public function preRemove(PlayerChart $playerChart, LifecycleEventArgs $event): void
    {
        // Chart
        $chart = $playerChart->getChart();
        $chart->setNbPost($chart->getNbPost() - 1);
        $chart->setStatusPlayer(ChartStatus::STATUS_MAJ);
        $chart->setStatusTeam(ChartStatus::STATUS_MAJ);

        // Player
        $player = $playerChart->getPlayer();
        $player->setNbChart($player->getNbChart() - 1);

        // Group
        $group = $chart->getGroup();
        $group->setNbPost($group->getNbPost() - 1);

        // Game
        $game = $group->getGame();
        $game->setNbPost($game->getNbPost() - 1);
    }
}
