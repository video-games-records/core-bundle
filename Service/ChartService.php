<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Repository\ChartRepository;

class ChartService
{
    private ChartRepository $chartRepository;

    public function __construct(ChartRepository $chartRepository)
    {
        $this->chartRepository = $chartRepository;
    }

    /**
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isMajPlayerRunning(): bool
    {
        return $this->chartRepository->isMajPlayerRunning();
    }

    /**
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isMajTeamRunning(): bool
    {
        return $this->chartRepository->isMajTeamRunning();
    }


    /**
     * @param int $nbChart
     * @throws DBALException
     */
    public function goToMajPlayer(int $nbChart = 100)
    {
        $this->chartRepository->goToMajPlayer($nbChart);
    }

    /**
     * @return Chart[]
     */
    public function getChartToMajPlayer(): array
    {
        return $this->chartRepository->getChartToMajPlayer();
    }

    /**
     * @throws DBALException
     */
    public function goToNormalPlayer()
    {
        $this->chartRepository->goToNormalPlayer();
    }

    /**
     * @param int $nbChart
     * @throws DBALException
     */
    public function goToMajTeam(int $nbChart = 100)
    {
        $this->chartRepository->goToMajTeam($nbChart);
    }

    /**
     * @return Chart[]
     */
    public function getChartToMajTeam(): array
    {
        return $this->chartRepository->getChartToMajTeam();
    }

    /**
     * @throws DBALException
     */
    public function goToNormalTeam()
    {
        $this->chartRepository->goToNormalPlayer();
    }
}
