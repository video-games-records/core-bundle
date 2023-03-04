<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Write;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\Interface\Ranking\RankingCommandInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class PlayerGameRankingHandler implements RankingCommandInterface
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle($mixed): void
    {
        /** @var Game $game */
        $game = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->find($mixed);
        if (null === $game) {
            return;
        }

        //----- delete
        $query = $this->em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerGame pg WHERE pg.game = :game');
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
            AND g.boolDlc = 0
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
                '' as rankPointChart,
                '' as rankMedal,
                SUM(pg.chartRank0) as chartRank0,
                SUM(pg.chartRank1) as chartRank1,
                SUM(pg.chartRank2) as chartRank2,
                SUM(pg.chartRank3) as chartRank3,
                SUM(pg.chartRank4) as chartRank4,
                SUM(pg.chartRank5) as chartRank5,
                SUM(pg.pointChart) as pointChart,
                SUM(pg.nbChart) as nbChart,
                SUM(pg.nbChartProven) as nbChartProven,
                MAX(pg.lastUpdate) as lastUpdate
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg
            JOIN pg.player p
            JOIN pg.group g
            WHERE g.game = :game
            GROUP BY p.id
            ORDER BY pointChart DESC");


        $query->setParameter('game', $game);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $row['lastUpdate'] = new \DateTime($row['lastUpdate']);
            if (isset($dataWithoutDlc[$row['id']])) {
                $row = array_merge($row, $dataWithoutDlc[$row['id']]);
            } else {
                $row['pointChartWithoutDlc'] = 0;
                $row['nbChartWithoutDlc'] = 0;
                $row['nbChartProvenWithoutDlc'] = 0;
            }
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        $list = Ranking::calculateGamePoints($list, ['rankPointChart', 'nbEqual'], 'pointGame', 'pointChart');
        $list = Ranking::order($list, ['chartRank0' => SORT_DESC, 'chartRank1' => SORT_DESC, 'chartRank2' => SORT_DESC, 'chartRank3' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3', 'chartRank4', 'chartRank5']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            $playerGame = $serializer->denormalize(
                $row, 'VideoGamesRecords\CoreBundle\Entity\PlayerGame'
            );
            $playerGame->setPlayer($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['id']));
            $playerGame->setGame($game);

            $this->em->persist($playerGame);
        }
        $this->em->flush();

        $event = new GameEvent($game);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::PLAYER_GAME_MAJ_COMPLETED);
    }

    public function getRankingPoints(int $id = null, array $options = []): array
    {
        $game = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->find($id);
        if (null === $game) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $options['player'] ?? null;
        $team = $options['team'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('pg')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerGame', 'pg')
            ->join('pg.player', 'p')
            ->addSelect('p')
            ->orderBy('pg.rankPointChart');

        $query->where('pg.game = :game')
            ->setParameter('game', $game);

        if ($team != null) {
            $query->andWhere('(p.team = :team)')
                ->setParameter('team', $team);
        } elseif (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pg.rankPointChart <= :maxRank OR pg.player = :player)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } elseif ($maxRank !== null) {
            $query->andWhere('pg.rankPointChart <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }
        return $query->getQuery()->getResult();
    }

    public function getRankingMedals(int $id = null, array $options = []): array
    {
        $game = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->find($id);
        if (null === $game) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $options['player'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('pg')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerGame', 'pg')
            ->join('pg.player', 'p')
            ->addSelect('p')
            ->orderBy('pg.rankMedal');

        $query->where('pg.game = :game')
            ->setParameter('game', $game);

        if (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pg.rankMedal <= :maxRank OR pg.player = :player)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } elseif ($maxRank !== null) {
            $query->andWhere('pg.rankMedal <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }
}
