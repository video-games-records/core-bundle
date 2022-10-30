<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Select;

use Doctrine\ORM\Exception\ORMException;

class PlayerRankingSelect extends DefaultRankingSelect
{
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
     * @throws ORMException
     */
    private function getRanking(string $column = 'rankPointChart', array $options = []): mixed
    {
        $maxRank = $options['maxRank'] ?? null;
        $player = $this->getPlayer();
        $team = !empty($options['idTeam'])? $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $options['idTeam']) : null;

        $query = $this->em->createQueryBuilder()
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
