<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Read;

use Doctrine\ORM\Exception\ORMException;

class PlayerRankingQuery extends DefaultRankingQuery
{
    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingPointChart(array $options = []): array
    {
        return $this->getRanking('rankPointChart', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingPointGame(array $options = []): array
    {
        return $this->getRanking('rankPointGame', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingMedals(array $options = []): array
    {
        return $this->getRanking('rankMedal', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingBadge(array $options = []): array
    {
        return $this->getRanking('rankBadge', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingCup(array $options = []): array
    {
        return $this->getRanking('rankCup', $options);
    }

    /**
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingProof(array $options = []): array
    {
        return $this->getRanking('rankProof', $options);
    }

    /**
     * @param string $column
     * @param array  $options
     * @return array
     * @throws ORMException
     */
    private function getRanking(string $column = 'rankPointChart', array $options = []): array
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
