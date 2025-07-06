<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerCountryRankingProvider;
use VideoGamesRecords\CoreBundle\Event\PlayerCountryUpdated;

final readonly class PlayerCountryUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private PlayerCountryRankingProvider $rankingProvider
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PlayerCountryUpdated::class => 'updateBadge',
        ];
    }

    /**
     * @param PlayerCountryUpdated $event
     */
    public function updateBadge(PlayerCountryUpdated $event): void
    {
        $country = $event->getCountry();

        if ($country->getBadge() === null) {
            return;
        }

        $ranking = $this->rankingProvider->getRankingPoints($country->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $player) {
            $players[$player->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')
            ->updateBadge($players, $country->getBadge());
    }
}
