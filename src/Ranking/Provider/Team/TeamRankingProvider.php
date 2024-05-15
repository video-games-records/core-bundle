<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Provider\Team;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class TeamRankingProvider
{
    protected EntityManagerInterface $em;
    protected UserProvider $userProvider;

    public function __construct(
        EntityManagerInterface $em,
        UserProvider $userProvider
    ) {
        $this->em = $em;
        $this->userProvider = $userProvider;
    }

    /**
     * @throws ORMException
     */
    protected function getTeam(): ?Team
    {
        if ($this->userProvider->getUser()) {
            return $this->userProvider->getTeam();
        }
        return null;
    }

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
     * @param string $column
     * @param array  $options
     * @return array
     * @throws ORMException
     */
    private function getRanking(string $column = 'rankPointChart', array $options = []): array
    {
        $maxRank = $options['maxRank'] ?? 100;
        $team = $this->getTeam();

        $query = $this->em->createQueryBuilder()
            ->select('t')
            ->from('VideoGamesRecords\CoreBundle\Entity\Team', 't')
            ->where("t.$column IS NOT NULL")
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
