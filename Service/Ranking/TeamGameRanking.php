<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Event\GameEvent;
use VideoGamesRecords\CoreBundle\Interface\RankingInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class TeamGameRanking implements RankingInterface
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function maj(int $id): void
    {
        $game = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->find($id);
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

    public function getRankingPoints(int $id = null, array $options = []): array
    {
        $game = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->find($id);
        if (null === $game) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $team = $options['team'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('tg')
            ->from('VideoGamesRecords\CoreBundle\Entity\TeamGame', 'tg')
            ->join('tg.team', 't')
            ->addSelect('t')
            ->orderBy('tg.rankPointChart');

        $query->where('tg.game = :game')
            ->setParameter('game', $game);

        if (($maxRank !== null) && ($team !== null)) {
            $query->andWhere('(tg.rankPointChart <= :maxRank OR tg.team = :team)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('team', $team);
        } elseif ($maxRank !== null) {
            $query->andWhere('tg.rankPointChart <= :maxRank')
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
        $team = $options['team'] ?? null;

         $query = $this->em->createQueryBuilder()
            ->select('tg')
            ->from('VideoGamesRecords\CoreBundle\Entity\TeamGame', 'tg')
            ->join('tg.team', 't')
            ->addSelect('t')
            ->orderBy('tg.rankMedal');

        $query->where('tg.game = :game')
            ->setParameter('game', $game);

        if (($maxRank !== null) && ($team !== null)) {
            $query->andWhere('(tg.rankMedal <= :maxRank OR tg.team = :team)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('team', $team);
        } elseif ($maxRank !== null) {
            $query->andWhere('tg.rankMedal <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }
}
