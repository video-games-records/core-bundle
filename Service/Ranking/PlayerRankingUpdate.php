<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Interface\RankingUpdateInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerRankingUpdate implements RankingUpdateInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function maj(int $id): void
    {
        $player = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->find($id);
        if (null === $player) {
            return;
        }
        
        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 SUM(pg.chartRank0) as chartRank0,
                 SUM(pg.chartRank1) as chartRank1,
                 SUM(pg.chartRank2) as chartRank2,
                 SUM(pg.chartRank3) as chartRank3,
                 SUM(pg.nbChart) as nbChart,
                 SUM(pg.nbChartProven) as nbChartProven,
                 SUM(pg.pointChart) as pointChart,
                 SUM(pg.pointGame) as pointGame,
                 COUNT(DISTINCT pg.game) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGame pg
            JOIN pg.player p
            JOIN pg.game g
            WHERE pg.player = :player
            AND g.boolRanking = 1
            GROUP BY p.id");
        $query->setParameter('player', $player);
        $row = $query->getOneOrNullResult();

        $player->setChartRank0($row['chartRank0']);
        $player->setChartRank1($row['chartRank1']);
        $player->setChartRank2($row['chartRank2']);
        $player->setChartRank3($row['chartRank3']);
        $player->setNbChart($row['nbChart']);
        $player->setNbChartProven($row['nbChartProven']);
        $player->setNbGame($row['nbGame']);
        $player->setPointChart($row['pointChart']);
        $player->setPointGame($row['pointGame']);

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
            AND g.boolRanking = 1
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
            AND g.boolRanking = 1
            GROUP BY p.id");

        $query->setParameter('player', $player);
        for ($i = 1; $i <= 3; $i++) {
            $query->setParameter('rank', $i);
            $row = $query->getOneOrNullResult();
            if ($row) {
                $data["gameRank$i"] = $row['nb'];
            }
        }

        $player->setGameRank0($data['gameRank0']);
        $player->setGameRank1($data['gameRank1']);
        $player->setGameRank2($data['gameRank2']);
        $player->setGameRank3($data['gameRank3']);

        $this->em->persist($player);
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function majRank(): void
    {
        $this->majRankPointChart();
        $this->majRankMedal();
    }
     /**
     * Update column rankPointChart
     */
    private function majRankPointChart()
    {
        $players = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->findBy(array(), array('pointChart' => 'DESC'));
        Ranking::addObjectRank($players, 'rankPointChart', array('pointChart'));
        $this->em->flush();
    }

    /**
     * Update column rankMedal
     */
    private function majRankMedal()
    {
        $players = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->findBy(array(), array('chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC'));
        Ranking::addObjectRank($players, 'rankMedal', array('chartRank0', 'chartRank1', 'chartRank2', 'chartRank3'));
        $this->em->flush();
    }
}
