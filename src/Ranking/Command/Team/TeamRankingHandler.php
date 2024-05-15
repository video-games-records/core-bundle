<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Command\Team;

use Doctrine\ORM\NonUniqueResultException;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Event\TeamEvent;
use VideoGamesRecords\CoreBundle\Ranking\Command\AbstractRankingHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

class TeamRankingHandler extends AbstractRankingHandler
{
    /**
     * @throws NonUniqueResultException
     */
    public function handle($mixed): void
    {
        /** @var Team $team */
        $team = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Team')->find($mixed);
        if (null == $team) {
            return;
        }

        $query = $this->em->createQuery("
            SELECT
                 t.id,
                 round(AVG(tg.rankPointChart),2) as averageGameRank,
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

            $team->setAverageGameRank((float) $row['averageGameRank']);
            $team->setChartRank0((int) $row['chartRank0']);
            $team->setChartRank1((int) $row['chartRank1']);
            $team->setChartRank2((int) $row['chartRank2']);
            $team->setChartRank3((int) $row['chartRank3']);
            $team->setPointChart((int) $row['pointChart']);
            $team->setPointGame((int) $row['pointGame']);
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
                $data['gameRank' . $i] = $row['nb'];
            }
        }

        $team->setGameRank0($data['gameRank0']);
        $team->setGameRank1($data['gameRank1']);
        $team->setGameRank2($data['gameRank2']);
        $team->setGameRank3($data['gameRank3']);

        // 3 Badge Ranking
        $query = $this->em->createQuery("
            SELECT
                 t.id,
                 COUNT(tb.badge) as nbMasterBadge,
                 SUM(b.value) as pointBadge
            FROM VideoGamesRecords\CoreBundle\Entity\TeamBadge tb
            JOIN tb.badge b
            JOIN tb.team t
            WHERE b.type = :type
            AND tb.team = :team
            AND tb.endedAt IS NULL
            GROUP BY t.id");
        $query->setParameter('type', 'Master');
        $query->setParameter('team', $team);

        $row = $query->getOneOrNullResult();
        if ($row) {
            $team->setNbMasterBadge($row['nbMasterBadge']);
            $team->setPointBadge((int) $row['pointBadge']);
        }

        $this->em->persist($team);
        $this->em->flush();

        $event = new TeamEvent($team);
        $this->eventDispatcher->dispatch($event, VideoGamesRecordsCoreEvents::TEAM_MAJ_COMPLETED);
    }
}
