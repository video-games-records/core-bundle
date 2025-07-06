<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerGameRankingProvider;
use VideoGamesRecords\CoreBundle\Event\PlayerGameUpdated;

final readonly class PlayerGameUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private PlayerGameRankingProvider $rankingProvider,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PlayerGameUpdated::class => 'updateBadge',
        ];
    }

    /**
     * @param PlayerGameUpdated $event
     * @throws ORMException
     */
    public function updateBadge(PlayerGameUpdated $event): void
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->rankingProvider->getRankingPoints($event->getGame()->getId(), ['maxRank' => 1]);
        $players = array();
        foreach ($ranking as $playerGame) {
            $players[$playerGame->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')
            ->updateBadge($players, $event->getGame()->getBadge());
    }
}
