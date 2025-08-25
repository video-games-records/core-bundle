<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\MessageHandler\Player;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Event\PlayerSerieUpdated;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerSerieRank;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

#[AsMessageHandler]
readonly class UpdatePlayerSerieRankHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws ORMException
     * @throws ExceptionInterface
     */
    public function __invoke(UpdatePlayerSerieRank $updatePlayerSerieRank): array
    {
        $serie = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Serie')->find(
            $updatePlayerSerieRank->getSerieId()
        );
        if (null === $serie) {
            return ['error' => 'serie not found'];
        }

        // Delete old data
        $query = $this->em->createQuery(
            'DELETE VideoGamesRecords\CoreBundle\Entity\PlayerSerie us WHERE us.serie = :serie'
        );
        $query->setParameter('serie', $serie);
        $query->execute();

        // Select data
        $query = $this->em->createQuery("
            SELECT
                p.id as idPlayer,
                '' as rankPointChart,
                '' as rankMedal,
                SUM(pg.chartRank0) as chartRank0,
                SUM(pg.chartRank1) as chartRank1,
                SUM(pg.chartRank2) as chartRank2,
                SUM(pg.chartRank3) as chartRank3,
                SUM(pg.chartRank4) as chartRank4,
                SUM(pg.chartRank5) as chartRank5,
                SUM(pg.pointGame) as pointGame,
                SUM(pg.pointChart) as pointChart,
                SUM(pg.pointChartWithoutDlc) as pointChartWithoutDlc,
                SUM(pg.nbChart) as nbChart,
                SUM(pg.nbChartWithoutDlc) as nbChartWithoutDlc,
                SUM(pg.nbChartProven) as nbChartProven,
                SUM(pg.nbChartProvenWithoutDlc) as nbChartProvenWithoutDlc,
                COUNT(DISTINCT pg.game) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.game g
            JOIN pg.player p
            WHERE g.serie = :serie
            GROUP BY p.id
            ORDER BY pointChart DESC");

        $query->setParameter('serie', $serie);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart']);
        $list = Ranking::order(
            $list,
            [
                'chartRank0' => SORT_DESC,
                'chartRank1' => SORT_DESC,
                'chartRank2' => SORT_DESC,
                'chartRank3' => SORT_DESC
            ]
        );
        $list = Ranking::addRank(
            $list,
            'rankMedal',
            ['chartRank0', 'chartRank1', 'chartRank2', 'chartRank3', 'chartRank4', 'chartRank5']
        );

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);


        foreach ($list as $row) {
            $playerSerie = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\PlayerSerie'
            );
            $playerSerie->setPlayer(
                $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['idPlayer'])
            );
            $playerSerie->setSerie($serie);

            $this->em->persist($playerSerie);
            $this->em->flush();
        }

        $this->eventDispatcher->dispatch(
            new PlayerSerieUpdated($serie)
        );
        return ['success' => true];
    }
}
