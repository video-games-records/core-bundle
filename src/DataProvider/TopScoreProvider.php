<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class TopScoreProvider
{
    protected EntityManagerInterface $em;
    protected UserProvider $userProvider;

    public function __construct(
        EntityManagerInterface $em,
        UserProvider $userProvider
    ) {
        $this->em = $em;
        $this->userProvider = $userProvider;
    }

    /**
     * @throws ORMException
     */
    protected function getPlayer(): ?Player
    {
        if ($this->userProvider->getUser()) {
            return $this->userProvider->getPlayer();
        }
        return null;
    }

    /**
     * @throws ORMException
     */
    public function load($group, string $locale = 'en'): mixed
    {
        $player = $this->getPlayer();
        $query = $this->em->createQueryBuilder()
            ->select('ch')
            ->from('VideoGamesRecords\CoreBundle\Entity\Chart', 'ch')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->addSelect('pc')
            ->andWhere('ch.group = :group')
            ->setParameter('group', $group);

        $column = ($locale == 'fr') ? 'libChartFr' : 'libChartEn';
        $query->orderBy("ch.$column", 'ASC');

        if ($player !== null) {
            $query->leftJoin('ch.playerCharts', 'pc', 'WITH', 'pc.rank = 1 OR pc.player = :player')
                ->setParameter('player', $player);
        } else {
            $query->leftJoin('ch.playerCharts', 'pc', 'WITH', 'pc.rank = 1');
        }
        $charts = $query->getQuery()->getResult();

        // Set top1 and player score
        foreach ($charts as $chart) {
            foreach ($chart->getPlayerCharts() as $playerChart) {
                if ($playerChart->getRank() == 1) {
                    $chart->setPlayerChart1($playerChart);
                }
                if (($player !== null) && ($playerChart->getPlayer()->getId() == $player->getId())) {
                    $chart->setPlayerChartP($playerChart);
                }
            }
        }
        return $charts;
    }
}
