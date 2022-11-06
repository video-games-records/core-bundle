<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Write;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use VideoGamesRecords\CoreBundle\Interface\Ranking\RankingCommandInterface;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\TeamChartRankingQuery;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class TeamChartRankingHandler implements RankingCommandInterface
{
    private EntityManagerInterface $em;
    private array $teams = [];
    private array $games = [];
    private array $groups = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle($mixed): void
    {
        $chart = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')->find($mixed);
        if (null === $chart) {
            return ;
        }

        $this->groups[$chart->getGroup()->getId()] = $chart->getGroup();
        $this->games[$chart->getGroup()->getGame()->getId()] = $chart->getGroup()->getGame();

        //----- delete
        $query = $this->em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\TeamChart tc WHERE tc.chart = :chart');
        $query->setParameter('chart', $chart);
        $query->execute();

        $query = $this->em->createQuery("
            SELECT pc
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.player p
            JOIN p.team t
            WHERE pc.chart = :chart
            ORDER BY pc.pointChart DESC");

        $query->setParameter('chart', $chart);
        $result = $query->getResult();

        $list = array();
        foreach ($result as $playerChart) {
            $team = $playerChart->getPlayer()->getTeam();
            $this->teams[$team->getId()] = $team;

            $idTeam = $team->getId();
            if (!isset($list[$idTeam])) {
                $list[$idTeam] = [
                    'idTeam' => $playerChart->getPlayer()->getTeam()->getId(),
                    'nbPlayer' => 1,
                    'pointChart' => $playerChart->getPointChart(),
                    'chartRank0' => 0,
                    'chartRank1' => 0,
                    'chartRank2' => 0,
                    'chartRank3' => 0,
                ];
            } elseif ($list[$idTeam]['nbPlayer'] < 5) {
                $list[$idTeam]['nbPlayer']   += 1;
                $list[$idTeam]['pointChart'] += $playerChart->getPointChart();
            }
        }

        //----- add some data
        $list = array_values($list);
        $list = Ranking::order($list, ['pointChart' => SORT_DESC]);
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        $nbTeam = count($list);

        foreach ($list as $row) {
            //----- add medals
            if ($row['rankPointChart'] == 1 && $row['nbEqual'] == 1 && $nbTeam > 1) {
                $row['chartRank0'] = 1;
                $row['chartRank1'] = 1;
            } elseif ($row['rankPointChart'] == 1 && $row['nbEqual'] == 1 && $nbTeam == 1) {
                $row['chartRank1'] = 1;
            } elseif ($row['rankPointChart'] == 1 && $row['nbEqual'] > 1) {
                $row['chartRank1'] = 1;
            } elseif ($row['rankPointChart'] == 2) {
                $row['chartRank2'] = 1;
            } elseif ($row['rankPointChart'] == 3) {
                $row['chartRank3'] = 1;
            }

            $teamChart = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\TeamChart'
            );
            $teamChart->setTeam($this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['idTeam']));
            $teamChart->setChart($chart);

            $this->em->persist($teamChart);
        }

        $this->em->flush();
    }

    public function getTeams(): array
    {
        return $this->teams;
    }

    public function getGames(): array
    {
        return $this->games;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }
}
