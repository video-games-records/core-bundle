<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Website;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

class GetStats extends AbstractController
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
    public function __invoke(): array
    {
        /** @var PlayerRepository $playerRepository */
        $playerRepository = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player');

        /** @var GameRepository $gameRepository */
        $gameRepository = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game');

        $playerStats = $playerRepository->getStats();
        $gameStats = $gameRepository->getStats();

        return array(
            'nbPlayer' => (int)$playerStats[1],
            'nbChart' => (int) $playerStats[2],
            'nbChartProven' => (int) $playerStats[3],
            'nbGame' => (int) $gameStats[1],
        );
    }
}
