<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\MessageHandler\Player;

use DateMalformedStringException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\PlayerGame;
use VideoGamesRecords\CoreBundle\Event\PlayerGameUpdated;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerData;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerGameRank;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerPlatformRank;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerRank;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerSerieRank;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use Zenstruck\Messenger\Monitor\Stamp\DescriptionStamp;

#[AsMessageHandler]
readonly class UpdatePlayerGameRankHandler
{
    private const int DELAY_SERIE_UPDATE = 3600000; // 1 heure
    private const int DELAY_PLATFORM_UPDATE = 21600000; // 6 heures


    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws ORMException
     * @throws ExceptionInterface|DateMalformedStringException
     */
    public function __invoke(UpdatePlayerGameRank $updatePlayerGameRank): array
    {
        /** @var Game $game */
        $game = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')
            ->find($updatePlayerGameRank->getGameId());
        if (null == $game) {
            return ['error' => 'game not found'];
        }

        //----- delete
        $query = $this->em->createQuery(
            'DELETE VideoGamesRecords\CoreBundle\Entity\PlayerGame pg WHERE pg.game = :game'
        );
        $query->setParameter('game', $game);
        $query->execute();

        //----- data without DLC
        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 SUM(pg.pointChart) as pointChartWithoutDlc,
                 SUM(pg.nbChart) as nbChartWithoutDlc,
                 SUM(pg.nbChartProven) as nbChartProvenWithoutDlc
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg
            JOIN pg.player p
            JOIN pg.group g
            WHERE g.game = :game
            AND g.isDlc = 0
            GROUP BY p.id");

        $dataWithoutDlc = [];

        $query->setParameter('game', $game);
        $result = $query->getResult();
        foreach ($result as $row) {
            $dataWithoutDlc[$row['id']] = $row;
        }

        //----- select and save result in array
        $query = $this->em->createQuery("
            SELECT
                p.id,
                SUM(pg.chartRank0) as chartRank0,
                SUM(pg.chartRank1) as chartRank1,
                SUM(pg.chartRank2) as chartRank2,
                SUM(pg.chartRank3) as chartRank3,
                SUM(pg.chartRank4) as chartRank4,
                SUM(pg.chartRank5) as chartRank5,
                SUM(pg.pointChart) as pointChart
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg
            JOIN pg.player p
            JOIN pg.group g
            WHERE g.game = :game
            AND g.isRank = 1
            GROUP BY p.id");


        $dataRank = [];
        $query->setParameter('game', $game);
        $result = $query->getResult();
        foreach ($result as $row) {
            $dataRank[$row['id']] = $row;
        }

        //----- select and save result in array
        $query = $this->em->createQuery("
            SELECT
                p.id,
                '' as rankPointChart,
                '' as rankMedal,
                SUM(pg.nbChart) as nbChart,
                SUM(pg.nbChartProven) as nbChartProven,
                MAX(pg.lastUpdate) as lastUpdate
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg
            JOIN pg.player p
            JOIN pg.group g
            WHERE g.game = :game
            GROUP BY p.id");


        $query->setParameter('game', $game);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $row['lastUpdate'] = new \DateTime($row['lastUpdate']);
            // $dataWithoutDlc
            if (isset($dataWithoutDlc[$row['id']])) {
                $row = array_merge($row, $dataWithoutDlc[$row['id']]);
            } else {
                $row['pointChartWithoutDlc'] = 0;
                $row['nbChartWithoutDlc'] = 0;
                $row['nbChartProvenWithoutDlc'] = 0;
            }
            // $dataRank
            if (isset($dataRank[$row['id']])) {
                $row = array_merge($row, $dataRank[$row['id']]);
            } else {
                $row['chartRank0'] = 0;
                $row['chartRank1'] = 0;
                $row['chartRank2'] = 0;
                $row['chartRank3'] = 0;
                $row['chartRank4'] = 0;
                $row['chartRank5'] = 0;
                $row['pointChart'] = 0;
            }
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::order($list, ['pointChart' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        $list = Ranking::calculateGamePoints($list, ['rankPointChart', 'nbEqual'], 'pointGame', 'pointChart');
        $list = Ranking::order(
            $list,
            [
                'chartRank0' => SORT_DESC,
                'chartRank1' => SORT_DESC,
                'chartRank2' => SORT_DESC,
                'chartRank3' => SORT_DESC
            ]
        );
        $list = Ranking::addRank(
            $list,
            'rankMedal',
            ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3', 'chartRank4', 'chartRank5']
        );

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            $playerGame = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\PlayerGame'
            );
            $playerGame->setPlayer($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['id']));
            $playerGame->setGame($game);

            $this->em->persist($playerGame);
        }

        //Stats
        $game->setNbPlayer(count($list));

        $this->em->flush();

        if ($game->getSerie()) {
            $this->bus->dispatch(
                new UpdatePlayerSerieRank(
                    $game->getSerie()->getId(),
                ),
                [
                    new DelayStamp(self::DELAY_SERIE_UPDATE),
                    new DescriptionStamp(
                        sprintf('Update player-ranking for serie [%d]', $game->getSerie()->getId())
                    )
                ]
            );
        }

        /** @var Platform $platform */
        foreach ($game->getPlatforms() as $platform) {
            $this->bus->dispatch(
                new UpdatePlayerPlatformRank($platform->getId()),
                [
                    new DelayStamp(self::DELAY_PLATFORM_UPDATE),
                    new DescriptionStamp(
                        sprintf('Update player-ranking for platform [%d]', $platform->getId())
                    )
                ]
            );
        }

        /** @var PlayerGame $playerGame */
        foreach ($game->getPlayerGame() as $playerGame) {
            $this->bus->dispatch(
                new UpdatePlayerData($playerGame->getPlayer()->getId()),
                [
                    new DescriptionStamp(
                        sprintf('Update player-data for player [%d]', $playerGame->getPlayer()->getId())
                    )
                ]
            );
        }

        $this->bus->dispatch(new UpdatePlayerRank());

        $this->eventDispatcher->dispatch(
            new PlayerGameUpdated($game)
        );

        return ['success' => true];
    }
}
