<?php

namespace VideoGamesRecords\CoreBundle\Handler\Ranking\Player;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Event\PlayerEvent;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class PlayerRankingHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function majAll()
    {
        $query = $this->em->createQuery("
            SELECT p
            FROM VideoGamesRecords\CoreBundle\Entity\Player p
            WHERE p.nbChart > 0"
        );
        $players = $query->getResult();
        foreach ($players as $player) {
            $this->handle($player->getId());
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function handle($mixed): void
    {
        /** @var Player $player */
        $player = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->find($mixed);
        if (null === $player) {
            return;
        }
        if ($player->getId() == 0) {
            return;
        }

        $query = $this->em->createQuery("
            SELECT
                 p.id,
                 SUM(g.nbChart) as nbChartMax,
                 round(AVG(pg.rankPointChart),2) as averageGameRank,
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

        $player->setNbChartMax($row['nbChartMax']);
        $player->setAverageGameRank($row['averageGameRank']);
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
                $data['gameRank' . $i] = $row['nb'];
            }
        }

        $player->setGameRank0($data['gameRank0']);
        $player->setGameRank1($data['gameRank1']);
        $player->setGameRank2($data['gameRank2']);
        $player->setGameRank3($data['gameRank3']);


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
            AND pb.ended_at IS NULL
            AND g.boolRanking = 1
            GROUP BY p.id");
        $query->setParameter('type', 'Master');
        $query->setParameter('player', $player);

        $row = $query->getOneOrNullResult();
        if ($row) {
            $player->setNbMasterBadge($row['nbMasterBadge']);
            $player->setPointBadge($row['pointBadge']);
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

        $event = new PlayerEvent($player);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::PLAYER_MAJ_COMPLETED);
    }

    /**
     * @return void
     */
    public function majRank(): void
    {
        $this->majRankPointChart();
        $this->majRankPointGame();
        $this->majRankMedal();
        $this->majRankCup();
    }

    /**
     * @return void
     */
    public function majRankPointChart(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('pointChart' => 'DESC'));
        Ranking::addObjectRank($players);
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function majRankMedal(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC'));
        Ranking::addObjectRank($players, 'rankMedal', array('chartRank0', 'chartRank1', 'chartRank2', 'chartRank3'));
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function majRankPointGame(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('pointGame' => 'DESC'));
        Ranking::addObjectRank($players, 'rankPointGame', array('pointGame'));
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function majRankCup(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('gameRank0' => 'DESC', 'gameRank1' => 'DESC', 'gameRank2' => 'DESC', 'gameRank3' => 'DESC'));
        Ranking::addObjectRank($players, 'rankCup', array('gameRank0', 'gameRank1', 'gameRank2', 'gameRank3'));
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function majRankProof(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('nbChartProven' => 'DESC'));
        Ranking::addObjectRank($players, 'rankProof', array('nbChartProven'));
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function majRankBadge(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('pointBadge' => 'DESC', 'nbMasterBadge' => 'DESC'));
        Ranking::addObjectRank($players, 'rankBadge', array('pointBadge', 'nbMasterBadge'));
        $this->em->flush();
    }

    private function getPlayerRepository(): EntityRepository
    {
        return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player');
    }
}
