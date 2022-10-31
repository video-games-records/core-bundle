<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Updater;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Event\TeamEvent;
use VideoGamesRecords\CoreBundle\Interface\RankingUpdaterInterface;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class TeamRankingUpdater implements RankingUpdaterInterface
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function maj(int $id): void
    {
        $team = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Team')->find($id);
        if (null === $team) {
            return;
        }

        $query = $this->em->createQuery("
            SELECT
                 t.id,
                 SUM(tg.chartRank0) as chartRank0,
                 SUM(tg.chartRank1) as chartRank1,
                 SUM(tg.chartRank2) as chartRank2,
                 SUM(tg.chartRank3) as chartRank3,
                 SUM(tg.pointChart) as pointChart,
                 SUM(tg.pointGame) as pointGame,
                 COUNT(DISTINCT tg.game) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGame tg
            JOIN tg.team t
            WHERE tg.team = :team
            GROUP BY t.id");

        $query->setParameter('team', $team);
        $result = $query->getResult();
        if ($result) {
            $row = $result[0];

            $team->setChartRank0($row['chartRank0']);
            $team->setChartRank1($row['chartRank1']);
            $team->setChartRank2($row['chartRank2']);
            $team->setChartRank3($row['chartRank3']);
            $team->setPointChart($row['pointChart']);
            $team->setPointGame($row['pointGame']);
            $team->setNbGame($row['nbGame']);


        }

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
                 t.id,
                 COUNT(tg.game) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGame tg
            JOIN tg.game g
            JOIN tg.team t
            WHERE g.nbTeam > 1
            AND tg.rankPointChart = 1
            AND tg.nbEqual = 1
            AND tg.team = :team
            GROUP BY t.id");

        $query->setParameter('team', $team);
        $row = $query->getOneOrNullResult();
        if ($row) {
            $data['gameRank0'] = $row['nb'];
        }

        //----- data rank1 to rank3
        $query = $this->em->createQuery("
            SELECT
                 t.id,
                 COUNT(tg.game) as nb
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGame tg
            JOIN tg.team t
            WHERE tg.rankPointChart = :rank
            AND tg.team = :team
            GROUP BY t.id");

        $query->setParameter('team', $team);

        for ($i = 1; $i <= 3; $i++) {
            $query->setParameter('rank', $i);
            $row = $query->getOneOrNullResult();
            if ($row) {
                $data["gameRank$i"] = $row['nb'];
            }
        }

        $team->setGameRank0($data['gameRank0']);
        $team->setGameRank1($data['gameRank1']);
        $team->setGameRank2($data['gameRank2']);
        $team->setGameRank3($data['gameRank3']);

        $this->em->persist($team);
        $this->em->flush();

        $event = new TeamEvent($team);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::TEAM_MAJ_COMPLETED);
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
     * Update column rankPointChart
     */
    private function majRankPointChart()
    {
        $teams = $this->getTeamRepository()->findBy(array(), array('pointChart' => 'DESC'));
        Ranking::addObjectRank($teams, 'rankPointChart', array('pointChart'));
        $this->em->flush();
    }

    /**
     * Update column rankPointGame
     */
    private function majRankPointGame()
    {
        $teams = $this->getTeamRepository()->findBy(array(), array('pointGame' => 'DESC'));
        Ranking::addObjectRank($teams, 'rankPointGame', array('pointGame'));
        $this->em->flush();
    }

    /**
     * Update column rankMedal
     */
    private function majRankMedal()
    {
        $teams = $this->getTeamRepository()->findBy(array(), array('chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC'));
        Ranking::addObjectRank($teams, 'rankMedal', array('chartRank0', 'chartRank1', 'chartRank2', 'chartRank3'));
        $this->em->flush();
    }

    /**
     * Update column rankCup
     */
    public function majRankCup()
    {
        $teams = $this->getTeamRepository()->findBy(array(), array('gameRank0' => 'DESC', 'gameRank1' => 'DESC', 'gameRank2' => 'DESC', 'gameRank3' => 'DESC'));
        Ranking::addObjectRank($teams, 'rankCup', array('gameRank0', 'gameRank1', 'gameRank2', 'gameRank3'));
        $this->em->flush();
    }

    private function getTeamRepository(): EntityRepository
    {
        return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Team');
    }
}
