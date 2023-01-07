<?php

namespace VideoGamesRecords\CoreBundle\Service\Group;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use VideoGamesRecords\CoreBundle\DataTransformer\TokenStorageToPlayerTransformer;
use VideoGamesRecords\CoreBundle\DataTransformer\TokenStorageToTeamTransformer;
use VideoGamesRecords\CoreBundle\Entity\Player;

class TopScoreProvider
{
    protected EntityManagerInterface $em;
    protected TokenStorageToPlayerTransformer $tokenStorageToPlayerTransformer;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        EntityManagerInterface $em,
        TokenStorageToPlayerTransformer $tokenStorageToPlayerTransformer,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $em;
        $this->tokenStorageToPlayerTransformer = $tokenStorageToPlayerTransformer;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @throws ORMException
     */
    protected function getPlayer(): ?Player
    {
        return $this->tokenStorageToPlayerTransformer->transform($this->tokenStorage->getToken());
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
