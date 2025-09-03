<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player;

use VideoGamesRecords\CoreBundle\DataProvider\Ranking\AbstractRankingProvider;

class PlayerPlatformRankingProvider extends AbstractRankingProvider
{
    /**
     * @param int|null $id
     * @param array $options
     * @return array
     */
    public function getRankingPoints(?int $id = null, array $options = []): array
    {
        $platform = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Platform')->find($id);
        if (null === $platform) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $this->getPlayer($options['user'] ?? null);

        $query = $this->em->createQueryBuilder()
            ->select('pp')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerPlatform', 'pp')
            ->join('pp.player', 'p')
            ->addSelect('p')
            ->orderBy('pp.rankPointPlatform');

        $query->where('pp.platform = :platform')
            ->setParameter('platform', $platform);

        if (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pp.rankPointPlatform <= :maxRank OR pp.player = :player OR p.id IN (:friends))')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player)
                ->setParameter('friends', $player->getFriends());
        } elseif ($maxRank !== null) {
            $query->andWhere('pp.rankPointPlatform <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param int|null $id
     * @param array $options
     * @return array
     */
    public function getRankingMedals(?int $id = null, array $options = []): array
    {
        return array();
    }
}
