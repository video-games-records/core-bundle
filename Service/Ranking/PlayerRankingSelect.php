<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Interface\RankingSelectInterface;

class PlayerRankingSelect implements RankingSelectInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRankingPoints(int $id = null, array $options = []): array
    {
        return $this->getRanking('rankPointChart', $options);
    }

    public function getRankingPointChart(int $id = null, array $options = []): array
    {
        return $this->getRankingPoints($id, $options);
    }

    public function getRankingPointGame(int $id = null, array $options = []): array
    {
        return $this->getRanking('rankPointGame', $options);
    }

    public function getRankingMedals(int $id = null, array $options = []): array
    {
        return $this->getRanking('rankMedal', $options);
    }

    /**
     * @param string $column
     * @param array  $options
     * @return float|int|mixed|string
     */
    private function getRanking(string $column = 'rankPointChart', array $options = [])
    {
        $maxRank = $options['maxRank'] ?? null;
        $player = $options['player'] ?? null;
        $team = $options['team'] ?? null;

        $query = $this->em->createQueryBuilder('p')
            ->select('p')
            ->from('VideoGamesRecords\CoreBundle\Entity\Player', 'p')
            ->orderBy("p.$column");

        if ($team !== null) {
            $query->andWhere('(p.team = :team)')
                ->setParameter('team', $team);
        } elseif ($player !== null) {
            $query->where("(p.$column <= :maxRank OR p = :player)")
                ->setParameter('maxRank', 100)
                ->setParameter('player', $player);
        } else {
            $query->where("p.$column <= :maxRank")
                ->setParameter('maxRank', $maxRank);
        }
        return $query->getQuery()->getResult();
    }
}
