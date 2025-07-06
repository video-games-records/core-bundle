<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Team\TeamSerieRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class TeamSerieBadgeHandler
{
    private EntityManagerInterface $em;
    private RankingProviderInterface $rankingProvider;

    public function __construct(
        EntityManagerInterface $em,
        #[Autowire(service: TeamSerieRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->em = $em;
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @throws ORMException
     */
    public function process(Serie $serie): void
    {
        if ($serie->getBadge() === null) {
            return;
        }

        if ($serie->getSerieStatus()->isInactive()) {
            return;
        }

        $ranking = $this->rankingProvider->getRankingPoints($serie->getId(), array('maxRank' => 1));

        $teams = array();
        foreach ($ranking as $teamSerie) {
            $teams[$teamSerie->getTeam()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamBadge')->updateBadge($teams, $serie->getBadge());
    }
}
