<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerPlatformRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;
use VideoGamesRecords\CoreBundle\Event\PlayerPlatformUpdated;

final readonly class PlayerPlatformUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private PlayerPlatformRankingProvider $rankingProvider
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PlayerPlatformUpdated::class => 'updateBadge',
        ];
    }

    /**
     * @param PlayerPlatformUpdated $platformUpdated
     * @throws ORMException
     */
    public function updateBadge(PlayerPlatformUpdated $platformUpdated): void
    {
        $platform = $platformUpdated->getPlatform();

        if ($platform->getBadge() === null) {
            return;
        }

        $ranking = $this->rankingProvider->getRankingPoints($platform->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $playerPlatform) {
            $players[$playerPlatform->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository(PlayerBadge::class)->updateBadge($players, $platform->getBadge());
    }
}
