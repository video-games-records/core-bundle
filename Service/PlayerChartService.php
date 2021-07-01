<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Repository\ChartRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerBadgeRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerGameRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerGroupRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;

class PlayerChartService
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
     * @throws ORMException
     */
    public function majInvestigation()
    {
        $list = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->getPlayerChartToDesactivate();
        $statusReference = $this->em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NOT_PROOVED);
        /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
        foreach ($list as $playerChart) {
            var_dump($playerChart->getId());
            $playerChart->setStatus($statusReference);
        }
        $this->em->flush();
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
        if ($chartRepository->isMajPlayerRunning()) {
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
        /** @var PlayerChartRepository $playerChartRepository */
        $playerChartRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerChart');
        /** @var PlayerGroupRepository $playerGroupRepository */
        $playerGroupRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup');
        /** @var PlayerGameRepository $playerGameRepository */
        $playerGameRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerGame');
        /** @var PlayerRepository $playerRepository */
        $playerRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Player');
        /** @var PlayerBadgeRepository $playerBadgeRepository */
        $playerBadgeRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge');


        $chartRepository->goToMajPlayer($nbChartToMaj);
        $charts = $chartRepository->getChartToMajPlayer();

        $playerList = [];
        $groupList  = [];
        $gameList   = [];

        foreach ($charts as $chart) {
            $idGroup = $chart->getGroup()->getId();
            $idGame  = $chart->getGroup()->getGame()->getId();
            //----- Player
            $playerList = array_merge($playerList, $playerChartRepository->maj($chart));
            //----- Group
            if (!isset($groupList[$idGroup])) {
                $groupList[$idGroup] = $chart->getGroup();
            }
            //----- Game
            if (!isset($gameList[$idGame])) {
                $gameList[$idGame] = $chart->getGroup()->getGame();
            }
        }

        //----- Maj group
        foreach ($groupList as $group) {
            $playerGroupRepository->maj($group);
        }

        //----- Maj game
        foreach ($gameList as $game) {
            $playerGameRepository->maj($game);
            $playerBadgeRepository->majMasterBadge($game);
        }

        //----- Maj player
        foreach ($playerList as $player) {
            $player->setBoolMaj(true);
        }

        //----- Maj rank country
        /*foreach ($countryList as $country) {
            $playerRepository->majRankCountry($country);
            $playerBadgeRepository->majCountryBadge($country);
        }*/

        $this->em->flush();
        return count($charts);
    }
}
