<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\MessageHandler\Player;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerCountryRank;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerData;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerRank;

#[AsMessageHandler]
readonly class UpdatePlayerDataHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
    ) {
    }

    /**
     * @throws ORMException|ExceptionInterface
     */
    public function __invoke(UpdatePlayerData $updatePlayerData): array
    {
        /** @var Player $player */
        $player = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')
            ->find($updatePlayerData->getPlayerId());

        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 round(AVG(pg.rankPointChart),2) as averageGameRank,
                 SUM(pg.chartRank0) as chartRank0,
                 SUM(pg.chartRank1) as chartRank1,
                 SUM(pg.chartRank2) as chartRank2,
                 SUM(pg.chartRank3) as chartRank3,
                 SUM(pg.pointChart) as pointChart,
                 SUM(pg.pointGame) as pointGame,
                 COUNT(DISTINCT pg.game) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.player p
            JOIN pg.game g
            WHERE pg.player = :player
            AND g.isRank = 1
            GROUP BY p.id");
        $query->setParameter('player', $player);
        $row = $query->getOneOrNullResult();

        $player->setAverageGameRank((float) $row['averageGameRank']);
        $player->setChartRank0((int) $row['chartRank0']);
        $player->setChartRank1((int) $row['chartRank1']);
        $player->setChartRank2((int) $row['chartRank2']);
        $player->setChartRank3((int) $row['chartRank3']);
        $player->setPointChart((int) $row['pointChart']);
        $player->setPointGame((int) $row['pointGame']);


        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 SUM(g.nbChart) as nbChartMax,
                 SUM(pg.nbChart) as nbChart,
                 SUM(pg.nbChartProven) as nbChartProven,
                 COUNT(DISTINCT pg.game) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.player p
            JOIN pg.game g
            WHERE pg.player = :player
            GROUP BY p.id");
        $query->setParameter('player', $player);
        $row = $query->getOneOrNullResult();

        $player->setNbChartMax((int) $row['nbChartMax']);
        $player->setNbChart((int) $row['nbChart']);
        $player->setNbChartProven((int) $row['nbChartProven']);
        $player->setNbGame($row['nbGame'] ?? 0);

        // 2 game Ranking
        $data = [
            'gameRank0' => 0,
            'gameRank1' => 0,
            'gameRank2' => 0,
            'gameRank3' => 0,
        ];

        //----- data rank0
        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 COUNT(pg.game) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.game g
            JOIN pg.player p
            WHERE pg.rankPointChart = 1
            AND pg.player = :player
            AND g.nbPlayer > 1
            AND g.isRank = 1
            AND pg.nbEqual = 1
            GROUP BY p.id");

        $query->setParameter('player', $player);
        $row = $query->getOneOrNullResult();
        if ($row) {
            $data['gameRank0'] = $row['nb'];
        }
        //----- data rank1 to rank3
        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 COUNT(pg.game) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.game g
            JOIN pg.player p
            WHERE pg.rankPointChart = :rank
            AND pg.player = :player
            AND g.isRank = 1
            GROUP BY p.id");

        $query->setParameter('player', $player);
        for ($i = 1; $i <= 3; $i++) {
            $query->setParameter('rank', $i);
            $row = $query->getOneOrNullResult();
            if ($row) {
                $data['gameRank' . $i] = $row['nb'];
            }
        }

        $player->setGameRank0($data['gameRank0'] ?? 0);
        $player->setGameRank1($data['gameRank1'] ?? 0);
        $player->setGameRank2($data['gameRank2'] ?? 0);
        $player->setGameRank3($data['gameRank3'] ?? 0);


        // 3 Badge Ranking
        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 COUNT(pb.badge) as nbMasterBadge,
                 SUM(b.value) as pointBadge
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerBadge pb
            JOIN pb.badge b
            JOIN b.game g
            JOIN pb.player p
            WHERE b.type = :type
            AND pb.player = :player
            AND pb.endedAt IS NULL
            AND g.isRank = 1
            GROUP BY p.id");
        $query->setParameter('type', 'Master');
        $query->setParameter('player', $player);

        $row = $query->getOneOrNullResult();
        if ($row) {
            $player->setNbMasterBadge($row['nbMasterBadge']);
            $player->setPointBadge((int) $row['pointBadge']);
        }

        // 4 nbChartWithPlatform
        $query = $this->em->createQuery("
            SELECT COUNT(pc) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            WHERE pc.player = :player
            AND pc.platform IS NOT NULL");
        $query->setParameter('player', $player);

        $nb = $query->getSingleScalarResult();
        $player->setNbChartWithPlatform($nb);
        $player->getCountry()?->setBoolMaj(true);

        $this->em->persist($player);
        $this->em->flush();

        if ($player->getCountry()) {
            $this->bus->dispatch(new UpdatePlayerCountryRank($player->getCountry()->getId()));
        }

        return ['success' => true];
    }
}
