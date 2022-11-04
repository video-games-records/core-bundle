<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Read;

use Doctrine\ORM\Exception\ORMException;

class PlatformRankingSelect extends DefaultRankingSelect
{
    /**
     * @throws ORMException
     */
    public function getRankingPoints(int $id = null, array $options = []): array
    {
        $platform = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Platform')->find($id);
        if (null === $platform) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $this->getPlayer();

        $query = $this->em->createQueryBuilder()
            ->select('pp')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerPlatform', 'pp')
            ->join('pp.player', 'p')
            ->addSelect('p')
            ->orderBy('pp.rankPointPlatform');

        $query->where('pp.platform = :platform')
            ->setParameter('platform', $platform);

        if (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pp.rankPointPlatform <= :maxRank OR pp.player = :player)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } elseif ($maxRank !== null) {
            $query->andWhere('pp.rankPointPlatform <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        }
        return $query->getQuery()->getResult();
    }
}
