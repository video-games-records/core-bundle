<?php

namespace VideoGamesRecords\CoreBundle\Ranking\Provider\Player;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class PlayerRankingProvider
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
    protected function getPlayer(): ?Player
    {
        if ($this->userProvider->getUser()) {
            return $this->userProvider->getPlayer();
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
        $maxRank = $options['maxRank'] ?? 100;
        $limit = $options['limit'] ?? null;
        $player = $this->getPlayer();
        $team = !empty($options['idTeam']) ? $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $options['idTeam']) : null;

        $query = $this->em->createQueryBuilder()
            ->select('p')
            ->from('VideoGamesRecords\CoreBundle\Entity\Player', 'p')
            ->leftJoin('p.team', 't')
            ->addSelect('t')
            ->leftJoin('p.country', 'c')
            ->addSelect('c')
            ->leftJoin('c.translations', 'trans')
            ->addSelect('trans')
            ->where("p.$column != 0")
            ->orderBy("p.$column");

        if ($team !== null) {
            $query->andWhere('(p.team = :team)')
                ->setParameter('team', $team);
        } elseif (($maxRank !== null) && ($player !== null)) {
            $query->andWhere("(p.$column <= :maxRank OR p = :player)")
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } else {
            $query->andWhere("p.$column <= :maxRank")
                ->setParameter('maxRank', $maxRank);
        }

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }
}
