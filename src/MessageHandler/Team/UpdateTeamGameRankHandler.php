<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\MessageHandler\Team;

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
use VideoGamesRecords\CoreBundle\Entity\PlayerGame;
use VideoGamesRecords\CoreBundle\Entity\TeamGame;
use VideoGamesRecords\CoreBundle\Event\PlayerGameUpdated;
use VideoGamesRecords\CoreBundle\Event\TeamGameUpdated;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerData;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerRank;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerSerieRank;
use VideoGamesRecords\CoreBundle\Message\Team\UpdateTeamData;
use VideoGamesRecords\CoreBundle\Message\Team\UpdateTeamGameRank;
use VideoGamesRecords\CoreBundle\Message\Team\UpdateTeamGroupRank;
use VideoGamesRecords\CoreBundle\Message\Team\UpdateTeamRank;
use VideoGamesRecords\CoreBundle\Message\Team\UpdateTeamSerieRank;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use Zenstruck\Messenger\Monitor\Stamp\DescriptionStamp;

#[AsMessageHandler]
readonly class UpdateTeamGameRankHandler
{
    private const int DELAY_SERIE_UPDATE = 3600000; // 1 heure

    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws ORMException
     * @throws ExceptionInterface
     */
    public function __invoke(UpdateTeamGameRank $updateTeamGameRank): array
    {
        /** @var Game $game */
        $game = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')
        ->find($updateTeamGameRank->getGameId());
        if (null == $game) {
            return ['error' => 'game not found'];
        }

        //----- delete
        $query = $this->em->createQuery(
            'DELETE VideoGamesRecords\CoreBundle\Entity\TeamGame tg WHERE tg.game = :game'
        );
        $query->setParameter('game', $game);
        $query->execute();

        //----- select ans save result in array
        $query = $this->em->createQuery("
            SELECT
                t.id,
                '' as rankPointChart,
                '' as rankMedal,
                SUM(tg.chartRank0) as chartRank0,
                SUM(tg.chartRank1) as chartRank1,
                SUM(tg.chartRank2) as chartRank2,
                SUM(tg.chartRank3) as chartRank3,
                SUM(tg.pointChart) as pointChart
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGroup tg
            JOIN tg.group g
            JOIN tg.team t
            WHERE g.game = :game
            GROUP BY t.id
            ORDER BY pointChart DESC");


        $query->setParameter('game', $game);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        $game->setNbTeam(count($list));

        //----- add some data
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        $list = Ranking::order(
            $list,
            [
            'chartRank0' => SORT_DESC,
            'chartRank1' => SORT_DESC,
            'chartRank2' => SORT_DESC,
            'chartRank3' => SORT_DESC
            ]
        );
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3']);
        $list = Ranking::calculateGamePoints($list, array('rankPointChart', 'nbEqual'), 'pointGame', 'pointChart');

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            if (isset($row['id'])) {
                $teamGame = $serializer->denormalize(
                    $row,
                    'VideoGamesRecords\CoreBundle\Entity\TeamGame'
                );
                $teamGame->setTeam($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['id']));
                $teamGame->setGame($game);

                $this->em->persist($teamGame);
            }
        }

        // Stats
        $game->setNbTeam(count($list));

        $this->em->flush();

        if ($game->getSerie()) {
            $this->bus->dispatch(
                new UpdateTeamSerieRank($game->getSerie()->getId()),
                [
                    new DelayStamp(self::DELAY_SERIE_UPDATE),
                    new DescriptionStamp(
                        sprintf('Update team-ranking for serie [%d]', $game->getSerie()->getId())
                    )
                ]
            );
        }

        /** @var TeamGame $teamGame */
        foreach ($game->getTeamGame() as $teamGame) {
            $this->bus->dispatch(new UpdateTeamData($teamGame->getTeam()->getId()));
        }

        $this->bus->dispatch(new UpdateTeamRank());

        $this->eventDispatcher->dispatch(
            new TeamGameUpdated($game)
        );
        return ['success' => true];
    }
}
