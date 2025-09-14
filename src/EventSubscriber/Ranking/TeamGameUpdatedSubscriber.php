<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Team\TeamGameRankingProvider;
use VideoGamesRecords\CoreBundle\Event\TeamGameUpdated;

final readonly class TeamGameUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TeamGameRankingProvider $rankingProvider,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TeamGameUpdated::class => 'updateBadge',
        ];
    }

    /**
     * @param TeamGameUpdated $event
     * @throws ORMException
     */
    public function updateBadge(TeamGameUpdated $event): void
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->rankingProvider->getRankingPoints($event->getGame()->getId(), ['maxRank' => 1]);
        $teams = array();
        foreach ($ranking as $teamGame) {
            $teams[$teamGame->getTeam()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamBadge')
            ->updateBadge($teams, $event->getGame()->getBadge());
    }
}
