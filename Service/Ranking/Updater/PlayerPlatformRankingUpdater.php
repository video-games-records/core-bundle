<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Updater;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Interface\RankingUpdateInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerPlatformRankingUpdater implements RankingUpdateInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function majAll()
    {
        $platforms = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Platform')->findAll();
        foreach ($platforms as $platform) {
            $this->maj($platform->getId());
        }
    }

    public function maj(int $id): void
    {
        $platform = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Platform')->find($id);
        if (null === $platform) {
            return;
        }
        
        // Delete old data
        $query = $this->em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerPlatform pp WHERE pp.platform = :platform');
        $query->setParameter('platform', $platform);
        $query->execute();

        // Select data
        $query = $this->em->createQuery("
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
            $playerPlatform->setPlayer($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['id']));
            $playerPlatform->setPlatform($platform);

            $this->em->persist($playerPlatform);
            $this->em->flush();
        }
    }

    public function getRankingPoints(int $id = null, array $options = []): array
    {
        $platform = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Platform')->find($id);
        if (null === $platform) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $options['player'] ?? null;

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

    public function getRankingMedals(int $id = null, array $options = []): array
    {
        return [];
    }
}
