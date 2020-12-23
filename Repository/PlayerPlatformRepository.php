<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;

class PlayerPlatformRepository extends EntityRepository
{

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
                '' as rankPointChart,
                '' as rankPointGame,
                '' as rankMedal,
                SUM(pg.chartRank0) as chartRank0,
                SUM(pg.chartRank1) as chartRank1,
                SUM(pg.chartRank2) as chartRank2,
                SUM(pg.chartRank3) as chartRank3,
                SUM(pg.chartRank4) as chartRank4,
                SUM(pg.chartRank5) as chartRank5,
                SUM(pg.pointGame) as pointGame,
                SUM(pg.pointChart) as pointChart,
                SUM(pg.nbChart) as nbChart,
                COUNT(DISTINCT g.id) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.player p
            JOIN pg.game g
            JOIN g.platforms pl
            WHERE pl.id = :idPlatform
            GROUP BY p.id
            ORDER BY pointChart DESC");

        $query->setParameter('idPlatform', $platform->getId());
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart']);
        $list = Ranking::order($list, ['pointGame' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankPointGame', ['pointGame']);
        $list = Ranking::order($list, ['chartRank0' => SORT_DESC, 'chartRank1' => SORT_DESC, 'chartRank2' => SORT_DESC, 'chartRank3' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankMedal', ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3', 'chartRank4', 'chartRank5']);

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
            $this->_em->flush($playerPlatform);
        }
    }
}
