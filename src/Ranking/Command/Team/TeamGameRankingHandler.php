<?php

namespace VideoGamesRecords\CoreBundle\Ranking\Command\Team;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\Handler\Ranking\AbstractRankingHandler;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class TeamGameRankingHandler extends AbstractRankingHandler
{
    public function handle($mixed): void
    {
        /** @var Game $game */
        $game = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->find($mixed);
        if (null === $game) {
            return;
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
        $list = Ranking::order($list, ['chartRank0' => SORT_DESC, 'chartRank1' => SORT_DESC, 'chartRank2' => SORT_DESC, 'chartRank3' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3']);
        $list = Ranking::calculateGamePoints($list, array('rankPointChart', 'nbEqual'), 'pointGame', 'pointChart');

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            if (isset($row['id'])) {
                $teamGame = $serializer->denormalize(
                    $row, 'VideoGamesRecords\CoreBundle\Entity\TeamGame'
                );
                $teamGame->setTeam($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['id']));
                $teamGame->setGame($game);

                $this->em->persist($teamGame);
            }
        }
        $this->em->flush();

        $event = new GameEvent($game);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::TEAM_GAME_MAJ_COMPLETED);
    }
}
