<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerSerieRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;
use VideoGamesRecords\CoreBundle\Event\PlayerSerieUpdated;

final readonly class PlayerSerieUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private PlayerSerieRankingProvider $rankingProvider,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PlayerSerieUpdated::class => 'updateBadge',
        ];
    }

    /**
     * @param PlayerSerieUpdated $event
     * @throws ORMException
     */
    public function updateBadge(PlayerSerieUpdated $event): void
    {
        $serie = $event->getSerie();

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

        $this->em->getRepository(PlayerBadge::class)->updateBadge($players, $serie->getBadge());
    }
}
