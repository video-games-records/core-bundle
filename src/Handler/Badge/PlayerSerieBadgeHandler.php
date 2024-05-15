<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Player\PlayerSerieRankingProvider;

class PlayerSerieBadgeHandler
{
    private EntityManagerInterface $em;
    private RankingProviderInterface $rankingProvider; //PlayerSerieRankingQuery

    public function __construct(
        EntityManagerInterface $em,
        #[Autowire(service: PlayerSerieRankingProvider::class)]
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

        $players = array();
        foreach ($ranking as $playerSerie) {
            $players[$playerSerie->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $serie->getBadge());
    }
}
