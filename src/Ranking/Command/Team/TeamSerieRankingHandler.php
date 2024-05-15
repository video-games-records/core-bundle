<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Command\Team;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Event\SerieEvent;
use VideoGamesRecords\CoreBundle\Ranking\Command\AbstractRankingHandler;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class TeamSerieRankingHandler extends AbstractRankingHandler
{
    public function handle($mixed): void
    {
        $serie = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Serie')->find($mixed);
        if (null === $serie) {
            return;
        }

        // Delete old data
        $query = $this->em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\TeamSerie us WHERE us.serie = :serie');
        $query->setParameter('serie', $serie);
        $query->execute();

        // Select data
        $query = $this->em->createQuery("
            SELECT
                t.id as idTeam,
                '' as rankPointChart,
                '' as rankMedal,
                SUM(tg.chartRank0) as chartRank0,
                SUM(tg.chartRank1) as chartRank1,
                SUM(tg.chartRank2) as chartRank2,
                SUM(tg.chartRank3) as chartRank3,
                SUM(tg.pointGame) as pointGame,
                SUM(tg.pointChart) as pointChart
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGame tg
            JOIN tg.game g
            JOIN tg.team t
            WHERE g.serie = :serie
            GROUP BY t.id
            ORDER BY pointChart DESC");

        $query->setParameter('serie', $serie);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart']);
        $list = Ranking::order($list, ['chartRank0' => SORT_DESC, 'chartRank1' => SORT_DESC, 'chartRank2' => SORT_DESC, 'chartRank3' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);


        foreach ($list as $row) {
            $teamSerie = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\TeamSerie'
            );
            $teamSerie->setTeam($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['idTeam']));
            $teamSerie->setSerie($serie);

            $this->em->persist($teamSerie);
            $this->em->flush();
        }

        $event = new SerieEvent($serie);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::TEAM_SERIE_MAJ_COMPLETED);
    }
}
