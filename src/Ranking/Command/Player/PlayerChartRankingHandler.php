<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Command\Player;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Event\PlayerChartEvent;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Player\PlayerChartRankingProvider;
use VideoGamesRecords\CoreBundle\Ranking\Command\AbstractRankingHandler;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class PlayerChartRankingHandler extends AbstractRankingHandler
{
    private PlayerChartRankingProvider $playerChartRankingProvider;
    private array $players = [];
    private array $series = [];
    private array $games = [];
    private array $groups = [];
    private array $countries = [];
    private array $platforms = [];

    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $eventDispatcher,
        PlayerChartRankingProvider $playerChartRankingProvider
    ) {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
        $this->playerChartRankingProvider = $playerChartRankingProvider;
        parent::__construct($em, $eventDispatcher);
    }

    public function handle($mixed): void
    {
        /** @var Chart $chart */
        $chart = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')->find($mixed);
        if (null == $chart) {
            return;
        }

        $game = $chart->getGroup()->getGame();
        $this->groups[$chart->getGroup()->getId()] = $chart->getGroup();
        $this->games[$game->getId()] = $game;

        if ($game->getSerie() !== null && $game->getSerie()->getSerieStatus()->isActive()) {
            $this->series[$game->getSerie()->getId()] = $game->getSerie();
        }

        $ranking     = $this->playerChartRankingProvider->getRanking($chart);
        $pointsChart = Ranking::chartPointProvider(count($ranking));

        $topScoreLibValue = '';
        $previousLibValue = '';
        $rank             = 1;
        $nbEqual          = 1;
        $playerChartEqual = [];

        $result = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerChart')->getPlatforms($chart);
        $platforms = [];
        foreach ($result as $row) {
            $platforms[$row['id']] = [
                'count' => $row['nb'],
                'points' => Ranking::platformPointProvider($row['nb']),
                'previousLibValue' => '',
                'rank' => 0,
                'nbEqual' => 1,
                'playerChartEqual' => [],
            ];
        }

        foreach ($ranking as $k => $item) {
            $libValue = '';
            /** @var PlayerChart $playerChart */
            $playerChart = $item[0];

            $platform = $playerChart->getPlatform();
            if (null !== $platform) {
                $this->platforms[$platform->getId()] = $platform;
            }

            $country = $playerChart->getPlayer()->getCountry();
            if (null !== $country) {
                $this->countries[$country->getId()] = $country;
            }

            $this->players[$playerChart->getPlayer()->getId()] = $playerChart->getPlayer();

            // Lost position ?
            $oldRank = $playerChart->getRank();
            $oldNbEqual = $playerChart->getNbEqual();
            $playerChart->setIsTopScore(false);

            foreach ($chart->getLibs() as $lib) {
                $libValue .= $item['value_' . $lib->getId()] . '/';
            }
            if ($k === 0) {
                // Premier élément => topScore
                $playerChart->setIsTopScore(true);
                $topScoreLibValue = $libValue;
            } else {
                if ($libValue === $topScoreLibValue) {
                    $playerChart->setIsTopScore(true);
                }
                if ($previousLibValue === $libValue) {
                    ++$nbEqual;
                } else {
                    $rank += $nbEqual;
                    $nbEqual = 1;
                    $playerChartEqual = [];
                }
            }
            // Platform point
            if ($playerChart->getPlatform() != null) {
                $idPlatForm = $playerChart->getPlatform()->getId();
                if ($platforms[$idPlatForm]['previousLibValue'] === $libValue) {
                    ++$platforms[$idPlatForm]['nbEqual'];
                } else {
                    $platforms[$idPlatForm]['rank'] += $platforms[$idPlatForm]['nbEqual'];
                    $platforms[$idPlatForm]['nbEqual'] = 1;
                    $platforms[$idPlatForm]['playerChartEqual'] = [];
                }
                $platforms[$idPlatForm]['playerChartEqual'][] = $playerChart;
            }

            $playerChartEqual[] = $playerChart;

            $playerChart->setNbEqual($nbEqual);
            $playerChart->setRank($rank);
            $playerChart->setPointChart((int) (
                    array_sum(
                        array_slice(array_values($pointsChart), $playerChart->getRank() - 1, $playerChart->getNbEqual())
                    ) / $playerChart->getNbEqual()
                ));

            if ($nbEqual > 1) {
                // Pour les égalités déjà passées on met à jour le nbEqual et l'attribution des points
                foreach ($playerChartEqual as $playerChartToModify) {
                    $playerChartToModify->setNbEqual($nbEqual);
                    $playerChartToModify->setPointChart($playerChart->getPointChart());
                }
            }

            if ($playerChart->getPlatform() != null) {
                $idPlatForm = $playerChart->getPlatform()->getId();
                $playerChart->setPointPlatform((int) (
                    array_sum(
                        array_slice(array_values($platforms[$idPlatForm]['points']), $platforms[$idPlatForm]['rank'] - 1, $platforms[$idPlatForm]['nbEqual'])
                    ) / $platforms[$idPlatForm]['nbEqual']
                ));
                if ($platforms[$idPlatForm]['nbEqual'] > 1) {
                    // Pour les égalités déjà passées on met à jour le nbEqual et l'attribution des points
                    foreach ($platforms[$idPlatForm]['playerChartEqual'] as $playerChartToModify) {
                        $playerChartToModify
                            ->setPointPlatform($playerChart->getPointPlatform());
                    }
                }
            } else {
                $playerChart->setPointPlatform(0);
            }


            $event = new PlayerChartEvent($playerChart, $oldRank, $oldNbEqual);
            $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::PLAYER_CHART_MAJ_COMPLETED);

            $previousLibValue = $libValue;

            // Platform point
            if ($playerChart->getPlatform() != null) {
                $platforms[$playerChart->getPlatform()->getId()]['previousLibValue'] = $libValue;
            }
        }
        $this->em->flush();
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getSeries(): array
    {
        return $this->series;
    }

    public function getGames(): array
    {
        return $this->games;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getCountries(): array
    {
        return $this->countries;
    }

    public function getPlatforms(): array
    {
        return $this->platforms;
    }
}
