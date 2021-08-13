<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;

class PlayerChartListener
{
    private $changeSet = array();

    /**
     * @param PlayerChart        $playerChart
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function prePersist(PlayerChart $playerChart, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $playerChart->setStatus($em->getReference('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus', 1));
        $playerChart->setLastUpdate(new DateTime());

        // Chart
        $chart = $playerChart->getChart();
        $chart->setNbPost($chart->getNbPost() + 1);
        $chart->setStatusPlayer(Chart::STATUS_MAJ);
        $chart->setStatusTeam(Chart::STATUS_MAJ);

        // Player
        $player = $playerChart->getPlayer();
        $player->setNbChart($player->getNbChart() + 1);
    }

    /**
     * @param PlayerChart        $playerChart
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PlayerChart $playerChart, PreUpdateEventArgs $event)
    {
        $this->changeSet = $event->getEntityChangeSet();
        if (array_key_exists('status', $this->changeSet)) {
            if (!in_array($this->changeSet['status'][1], array(PlayerChartStatus::ID_STATUS_DEMAND, PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF))) {
                $playerChart->getChart()->setStatusPlayer(Chart::STATUS_MAJ);
            }
        }

        // Update by player
        if (array_key_exists('lastUpdate', $this->changeSet)) {
            $chart = $playerChart->getChart();
            $chart->setStatusPlayer(Chart::STATUS_MAJ);
            $chart->setStatusTeam(Chart::STATUS_MAJ);
        }

        if (array_key_exists('platform', $this->changeSet)) {
            $chart = $playerChart->getChart();
            $chart->setStatusPlayer(Chart::STATUS_MAJ);
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
     * @param PlayerChart            $playerChart
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postRemove(PlayerChart $playerChart,  LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
         // Chart
        $chart = $playerChart->getChart();
        $chart->setNbPost($chart->getNbPost() - 1);
        $chart->setStatusPlayer(Chart::STATUS_MAJ);
        $chart->setStatusTeam(Chart::STATUS_MAJ);

        // Player
        $player = $playerChart->getPlayer();
        $player->setNbChart($player->getNbChart() - 1);

        $em->flush();
    }
}
