<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Manager\ScoreManager;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;

class PlayerChartListener
{
    private array $changeSet = array();

    public function __construct(
        private readonly ScoreManager $scoreManager
    ) {
    }

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
        $this->incrementeNbPost($chart);
        $chart->setStatusPlayer(ChartStatus::MAJ);
        $chart->setStatusTeam(ChartStatus::MAJ);

        // Group
        $group = $chart->getGroup();

        // Game
        $game = $group->getGame();
        $game->setLastUpdate(new DateTime());
        $game->setLastScore($playerChart);

        // Player
        $player = $playerChart->getPlayer();
        $player->setNbChart($player->getNbChart() + 1);

        // Set platform
        if (null === $playerChart->getPlatform()) {
            $playerChart->setPlatform($this->scoreManager->getPlatform($player, $game));
        }

        if (!$this->scoreManager->hasScoreOnGroup($group, $player)) {
            $group->setNbPlayer($group->getNbPlayer() + 1);
        }

        if (!$this->scoreManager->hasScoreOnGame($game, $player)) {
            $game->setNbPlayer($game->getNbPlayer() + 1);
        }

        /*if (null === $playerChart->getPlatform()) {
            throw new BadRequestException('Missing platform');
        }*/
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
            $game = $playerChart->getChart()->getGroup()->getGame();
            $game->setLastUpdate(new DateTime());
            $game->setLastScore($playerChart);
        }

        if (array_key_exists('platform', $this->changeSet)) {
            $chart = $playerChart->getChart();
            $chart->setStatusPlayer(ChartStatus::MAJ);
        }

        // Move score
        if (array_key_exists('chart', $this->changeSet)) {
            $newChart = $this->changeSet['chart'][1];
            $oldChart = $this->changeSet['chart'][0];

            $this->incrementeNbPost($newChart);
            $this->decrementeNbPost($oldChart);

            $newChartLibs = $newChart->getLibs();
            foreach ($playerChart->getLibs() as $lib) {
                $lib->setLibChart($newChartLibs->current());
            }
        }

        $playerChart->setIsTopScore(false);
        if ($playerChart->getRank() === 1) {
            $playerChart->setIsTopScore(true);
        }

        if ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL) {
            $playerChart->setProof(null);
        }

        //-- status
        if ($playerChart->getStatus()->getId() == PlayerChartStatus::ID_STATUS_NOT_PROOVED) {
            $playerChart->setPointChart(0);
            $playerChart->setRank(0);
            $playerChart->setIsTopScore(false);
        }

        $this->updateDateInvestigation($playerChart);

        $this->updateProof($playerChart, $em);
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
            $chart->setStatusPlayer(ChartStatus::MAJ);
            $chart->setStatusTeam(ChartStatus::MAJ);
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
    /**
     * @param PlayerChart            $playerChart
     * @param LifecycleEventArgs $event
     */
    public function preRemove(PlayerChart $playerChart, LifecycleEventArgs $event): void
    {
        // Chart
        $chart = $playerChart->getChart();
        $this->decrementeNbPost($chart);
        $chart->setStatusPlayer(ChartStatus::MAJ);
        $chart->setStatusTeam(ChartStatus::MAJ);

        // Player
        $player = $playerChart->getPlayer();
        $player->setNbChart($player->getNbChart() - 1);
    }


    /**
     * @param PlayerChart $playerChart
     * @return void
     */
    private function updateDateInvestigation(PlayerChart $playerChart): void
    {
        if (
            null === $playerChart->getDateInvestigation()
            && PlayerChartStatus::ID_STATUS_INVESTIGATION === $playerChart->getStatus()->getId()
        ) {
            $playerChart->setDateInvestigation(new DateTime());
        }

        if (
            null !== $playerChart->getDateInvestigation()
            && in_array(
                $playerChart->getStatus()->getId(),
                [PlayerChartStatus::ID_STATUS_PROOVED, PlayerChartStatus::ID_STATUS_NOT_PROOVED],
                true
            )
        ) {
            $playerChart->setDateInvestigation(null);
        }
    }

    /**
     * @param PlayerChart $playerChart
     * @param EntityManagerInterface $em
     * @return void
     */
    private function updateProof(PlayerChart $playerChart, EntityManagerInterface $em): void
    {
        if (
            array_key_exists('proof', $this->changeSet)
            && $this->changeSet['proof'][1] !== null
            && $playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_DEMAND_SEND_PROOF
        ) {
            $proofRequest = $em->getRepository('VideoGamesRecords\CoreBundle\Entity\ProofRequest')
                ->findOneBy(
                    [
                        'playerChart' => $playerChart
                    ],
                    array('createdAt' => 'DESC')
                );

            if ($proofRequest) {
                $playerChart->getProof()->setProofRequest($proofRequest);
            }
        }
    }


    private function incrementeNbPost(Chart $chart): void
    {
        // Chart
        $chart->setNbPost($chart->getNbPost() + 1);
        // Group
        $group = $chart->getGroup();
        $group->setNbPost($group->getNbPost() + 1);
        // Game
        $game = $group->getGame();
        $game->setNbPost($game->getNbPost() + 1);
    }
    private function decrementeNbPost(Chart $chart): void
    {
        // Chart
        $chart->setNbPost($chart->getNbPost() - 1);
        // Group
        $group = $chart->getGroup();
        $group->setNbPost($group->getNbPost() - 1);
        // Game
        $game = $group->getGame();
        $game->setNbPost($game->getNbPost() - 1);
    }
}
