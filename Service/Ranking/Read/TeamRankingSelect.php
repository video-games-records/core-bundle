<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Read;

use Doctrine\ORM\Exception\ORMException;

class TeamRankingSelect extends DefaultRankingSelect
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
    public function getRankingCup(array $options = []): array
    {
        return $this->getRanking('rankCup', $options);
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
     * @param string $column
     * @param array  $options
     * @return array
     * @throws ORMException
     */
    private function getRanking(string $column = 'rankPointChart', array $options = []): array
    {
        $maxRank = $options['maxRank'] ?? null;
        $team = $this->getTeam();

        $query = $this->em->createQueryBuilder()
            ->select('t')
            ->from('VideoGamesRecords\CoreBundle\Entity\Team', 't')
            ->where("(t.$column != 0)")
            ->orderBy("t.$column");

        if ($team !== null) {
            $query->andWhere("(t.$column <= :maxRank OR t = :team)")
                ->setParameter('maxRank', $maxRank)
                ->setParameter('team', $team);
        } else {
            $query->andWhere("t.$column <= :maxRank")
                ->setParameter('maxRank', $maxRank);
        }
        return $query->getQuery()->getResult();
    }
}
