<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerChartRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerCountryRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerGameRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerGroupRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerPlatformRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerSerieRankingHandler;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class ScoringPlayerRankingHandler
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PlayerChartRankingHandler $playerChartRankingHandler,
        private readonly PlayerGroupRankingHandler $playerGroupRankingHandler,
        private readonly PlayerGameRankingHandler $playerGameRankingHandler,
        private readonly PlayerSerieRankingHandler $playerSerieRankingHandler,
        private readonly PlayerRankingHandler $playerRankingHandler,
        private readonly PlayerCountryRankingHandler $playerCountryRankingHandler,
        private readonly PlayerPlatformRankingHandler $playerPlatformRankingHandler,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @return int
     * @throws NonUniqueResultException|NoResultException
     */
    public function handle(): int
    {
        $charts = $this->getChartsToUpdate();

        /** @var Chart $chart */
        foreach ($charts as $chart) {
            $this->playerChartRankingHandler->handle($chart->getId());
            $chart->setStatusPlayer(ChartStatus::NORMAL);
        }

        $groups = $this->playerChartRankingHandler->getGroups();
        $games = $this->playerChartRankingHandler->getGames();
        $players = $this->playerChartRankingHandler->getPlayers();
        $series = $this->playerChartRankingHandler->getSeries();
        $countries = $this->playerChartRankingHandler->getCountries();
        $platforms = $this->playerChartRankingHandler->getPlatforms();

        //----- Maj group
        foreach ($groups as $group) {
            $this->playerGroupRankingHandler->handle($group->getId());
        }

        //----- Maj game
        foreach ($games as $game) {
            $this->playerGameRankingHandler->handle($game->getId());
        }

        //----- Maj serie
        foreach ($series as $serie) {
            $this->playerSerieRankingHandler->handle($serie->getId());
        }

        //----- Maj player
        foreach ($players as $player) {
            $this->playerRankingHandler->handle($player->getId());
        }

        //----- Maj platform
        foreach ($platforms as $platform) {
            $this->playerPlatformRankingHandler->handle($platform->getId());
        }

        //----- Maj country
        foreach ($countries as $country) {
            $this->playerCountryRankingHandler->handle($country->getId());
        }

        $event = new Event();
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::SCORES_PLAYER_MAJ_COMPLETED);

        echo sprintf("%d charts updated\n", count($charts));
        echo sprintf("%d groups updated\n", count($groups));
        echo sprintf("%d games updated\n", count($games));
        echo sprintf("%d series updated\n", count($series));
        echo sprintf("%d players updated\n", count($players));
        echo sprintf("%d platforms updated\n", count($platforms));
        echo sprintf("%d countries updated\n", count($countries));
        return 0;
    }


    private function getChartsToUpdate()
    {
        $query = $this->em->createQueryBuilder()
            ->select('ch')
            ->from('VideoGamesRecords\CoreBundle\Entity\Chart', 'ch')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->where('ch.statusPlayer = :status')
            ->setParameter('status', ChartStatus::MAJ)
            ->setMaxResults(100);

        return $query->getQuery()->getResult();
    }
}
