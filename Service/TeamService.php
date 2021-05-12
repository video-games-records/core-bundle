<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

class TeamService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws DBALException
     */
    public function maj()
    {
        /** @var TeamRepository $teamRepository */
        $teamRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Team');
        $teamRepository->majGameRank();
        $teamRepository->majRankPointChart();
        $teamRepository->majRankPointGame();
        $teamRepository->majRankMedal();
        $teamRepository->majRankCup();
    }

     /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function majRankBadge()
    {
        /** @var TeamRepository $teamRepository */
        $teamRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Team');
        $teamRepository->majPointBadge();
        $teamRepository->majRankBadge();
    }
}
