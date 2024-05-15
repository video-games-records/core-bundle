<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Player\PlayerPlatformRankingProvider;

class PlayerPlatformBadgeHandler
{
    private EntityManagerInterface $em;
    private RankingProviderInterface $rankingProvider; //PlayerPlatformRankingQuery

    public function __construct(
        EntityManagerInterface $em,
        #[Autowire(service: PlayerPlatformRankingProvider::class)]
        RankingProviderInterface $rankingProvider
    ) {
        $this->em = $em;
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @throws ORMException
     */
    public function process(Platform $platform): void
    {
        if ($platform->getBadge() === null) {
            return;
        }

        $ranking = $this->rankingProvider->getRankingPoints($platform->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $playerPlatform) {
            $players[$playerPlatform->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $platform->getBadge());
    }
}
