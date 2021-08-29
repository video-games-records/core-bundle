<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\PlayerPlatform;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;

class PlayerPlatformRepository extends EntityRepository
{
    /**
     * @param Platform $platform
     * @param null $maxRank
     * @param null $player
     * @return PlayerPlatform[]
     */
    public function getRankingPointPlatform(Platform $platform, $maxRank = null, $player = null): array
    {
        $query = $this->createQueryBuilder('pp')
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

    /**
     * @param $platform
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ExceptionInterface
     */
    public function maj($platform)
    {
        // Delete old data
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerPlatform pp WHERE pp.platform = :platform');
        $query->setParameter('platform', $platform);
        $query->execute();

        // Select data
        $query = $this->_em->createQuery("
            SELECT
                p.id,
                ifnull(SUM(pc.pointPlatform), 0) as pointPlatform,
                COUNT(pc) as nbChart
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN pc.platform pl
            WHERE pl.id = :idPlatform
            GROUP BY p.id
            ORDER BY pointPlatform DESC");

        $query->setParameter('idPlatform', $platform->getId());
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        $list = Ranking::addRank($list, 'rankPointPlatform', ['pointPlatform']);
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            $playerPlatform = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\PlayerPlatform'
            );
            $playerPlatform->setPlayer($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['id']));
            $playerPlatform->setPlatform($platform);

            $this->_em->persist($playerPlatform);
            $this->_em->flush();
        }
    }
}
