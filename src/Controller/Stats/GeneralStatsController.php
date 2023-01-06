<?php

namespace VideoGamesRecords\CoreBundle\Controller\Stats;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

class StatsController extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     * @throws NonUniqueResultException
     */
    public function getWebsiteStats(): array
    {
        /** @var PlayerRepository $playerRepository */
        $playerRepository = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player');

        /** @var GameRepository $gameRepository */
        $gameRepository = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game');

        /** @var TeamRepository $teamRepository */
        $teamRepository = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player');

        $playerStats = $playerRepository->getStats();
        $gameStats = $gameRepository->getStats();
        $teamStats = $teamRepository->getStats();

        return array(
            'nbPlayer' => $playerStats[1],
            'nbChart' => $playerStats[2],
            'nbChartProven' => $playerStats[3],
            'nbGame' => $gameStats[1],
            'nbTeam' => $teamStats[1],
        );
    }
}
