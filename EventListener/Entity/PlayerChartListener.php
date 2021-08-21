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
     * @throws ORMException
     */
    public function preUpdate(PlayerChart $playerChart, PreUpdateEventArgs $event)
    {
        $this->changeSet = $event->getEntityChangeSet();
        $em = $event->getEntityManager();

        if (array_key_exists('status', $this->changeSet)) {
            if ($this->changeSet['status'][1]->getId() == PlayerChartStatus::ID_STATUS_NOT_PROOVED) {
                $chart = $playerChart->getChart();
                $chart->setStatusPlayer(Chart::STATUS_MAJ);
                $chart->setStatusTeam(Chart::STATUS_MAJ);
            }
        }

        // Update by player
        if (array_key_exists('lastUpdate', $this->changeSet)) {
            $playerChart->setStatus($em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL));
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
     * @param PlayerChart              $playerChart
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postUpdate(PlayerChart $playerChart, LifecycleEventArgs $event)
    {
        if (array_key_exists('lastUpdate', $this->changeSet)) {
            $chart = $playerChart->getChart();
            $chart->setStatusPlayer(Chart::STATUS_MAJ);
            $chart->setStatusTeam(Chart::STATUS_MAJ);
            $event->getEntityManager()->flush();
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
