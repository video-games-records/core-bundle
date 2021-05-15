<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Repository\ChartRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamChartRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamGameRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamGroupRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

class TeamChartService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

     /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isMajRunning(): bool
    {
        /** @var ChartRepository $chartRepository */
        $chartRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Chart');
        if ($chartRepository->isMajTeamRunning()) {
            return true;
        }
        return false;
    }

    /**
     * @param int $nbChartToMaj
     * @return int
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ExceptionInterface
     */
    public function majRanking(int $nbChartToMaj = 100): int
    {
        /** @var ChartRepository $chartRepository */
        $chartRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Chart');
        /** @var TeamChartRepository $teamChartRepository */
        $teamChartRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamChart');
        /** @var TeamGroupRepository $teamGroupRepository */
        $teamGroupRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamGroup');
        /** @var TeamGameRepository $teamGameRepository */
        $teamGameRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamGame');
        /** @var TeamRepository $teamRepository */
        $teamRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Team');
        /** @var TeamBadgeRepository $teamBadgeRepository */
        $teamBadgeRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:TeamBadge');

        $chartRepository->goToMajTeam($nbChartToMaj);
        $charts = $chartRepository->getChartToMajTeam();

        $teamList = array();
        $groupList = array();
        $gameList = array();

        foreach ($charts as $chart) {
            $groupId = $chart->getGroup()->getId();
            $gameId = $chart->getGroup()->getGame()->getId();
            $teamList = array_unique(
                array_merge($teamList, $teamChartRepository->maj($chart))
            );

            //----- Group
            if (!isset($groupList[$groupId])) {
                $groupList[$groupId] = $chart->getGroup();
            }
            //----- Game
            if (!isset($gameList[$gameId])) {
                $gameList[$gameId] = $chart->getGroup()->getGame();
            }
        }

        //----- Maj group
        foreach ($groupList as $group) {
            $teamGroupRepository->maj($group);
        }

        //----- Maj game
        foreach ($gameList as $game) {
            $teamGameRepository->maj($game);
            $teamBadgeRepository->majMasterBadge($game);
        }

        //----- Maj team
        foreach ($teamList as $team) {
            $teamRepository->maj($team);
        }

        return count($charts);
    }
}
