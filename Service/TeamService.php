<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

class TeamService
{
    private TeamRepository $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws DBALException
     */
    public function maj()
    {
        $this->teamRepository->majGameRank();
        $this->teamRepository->majRankPointChart();
        $this->teamRepository->majRankPointGame();
        $this->teamRepository->majRankMedal();
        $this->teamRepository->majRankCup();
    }

     /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function majRankBadge()
    {
        $this->teamRepository->majPointBadge();
        $this->teamRepository->majRankBadge();
    }
}
