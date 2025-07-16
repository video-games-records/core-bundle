<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventSubscriber\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Team\TeamSerieRankingProvider;
use VideoGamesRecords\CoreBundle\Event\TeamSerieUpdated;

final readonly class TeamSerieUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TeamSerieRankingProvider $rankingProvider,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TeamSerieUpdated::class => 'updateBadge',
        ];
    }

    /**
     * @param TeamSerieUpdated $event
     * @throws ORMException
     */
    public function updateBadge(TeamSerieUpdated $event): void
    {
        $serie = $event->getSerie();

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

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\TeamBadge')
            ->updateBadge($teams, $serie->getBadge());
    }
}
