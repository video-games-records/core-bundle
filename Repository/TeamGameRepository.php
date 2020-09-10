<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

/**
 * TeamGameRepository
 */
class TeamGameRepository extends EntityRepository
{

    /**
     * @param Game $game
     * @param int $maxRank
     * @param Team $team
     * @return array
     */
    public function getRankingPoints($game, $maxRank = null, $team = null)
    {
        $query = $this->createQueryBuilder('tg')
            ->join('tg.team', 't')
            ->addSelect('t')//----- for using ->getTeam() on each result
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


    /**
     * @param Game $game
     * @param int $maxRank
     * @param Team $team
     * @return array
     */
    public function getRankingMedals($game, $maxRank = null, $team = null)
    {
        $query = $this->createQueryBuilder('tg')
            ->join('tg.team', 't')
            ->addSelect('t')//----- for using ->getTeam() on each result
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

    /**
     * @param $game
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function maj($game)
    {
        //----- delete
        $query = $this->_em->createQuery(
            'DELETE VideoGamesRecords\CoreBundle\Entity\TeamGame tg WHERE tg.game = :game'
        );
        $query->setParameter('game', $game);
        $query->execute();

        //----- select ans save result in array
        $query = $this->_em->createQuery("
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
            $teamGame = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\TeamGame'
            );
            $teamGame->setTeam($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['id']));
            $teamGame->setGame($game);

            $this->_em->persist($teamGame);
        }
        $this->_em->flush();
    }
}
