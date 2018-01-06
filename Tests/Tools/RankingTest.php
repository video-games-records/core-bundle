<?php

namespace Tests\VideoGamesRecords\CoreBundle\Tests\Tools;

use PHPUnit\Framework\TestCase;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class RankingTest extends TestCase
{
    /**
     * @dataProvider arrayPointRecordProvider
     *
     * @param int $nbParticipant
     */
    public function testArrayPointRecord($nbParticipant)
    {
        $pointsRanking = Ranking::chartPointProvider($nbParticipant);
        $this->assertCount($nbParticipant, $pointsRanking);
    }

    /**
     * @dataProvider calculateGamePointsProvider
     *
     * @param array $list
     */
    public function testCalculateGamePoints(array $list)
    {
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        foreach ($list as $ranking) {
            $this->assertArrayHasKey('nbEqual', $ranking);
            $this->assertArrayHasKey('rankPointChart', $ranking);
        }

        $list = Ranking::calculateGamePoints($list, ['rankPointChart', 'nbEqual'], 'pointGame', 'pointChart');
        foreach ($list as $ranking) {
            $this->assertArrayHasKey('pointGame', $ranking);
        }
    }

    public function arrayPointRecordProvider()
    {
        return [
            [1],
            [5],
            [500],
        ];
    }

    public function calculateGamePointsProvider()
    {
        return [
            [
                [
                    [
                        'idPlayer'       => 1,
                        'idGame'         => 1,
                        'rankMedal'      => 1,
                        'chartRank0'     => 1,
                        'chartRank1'     => 2,
                        'chartRank2'     => 3,
                        'chartRank3'     => 1,
                        'chartRank4'     => 2,
                        'chartRank5'     => 3,
                        'pointChart'     => 1,
                        'nbChart'        => 1,
                        'nbChartProven'  => 1,
                    ],
                    [
                        'idPlayer'       => 2,
                        'idGame'         => 1,
                        'rankMedal'      => 1,
                        'chartRank0'     => 1,
                        'chartRank1'     => 2,
                        'chartRank2'     => 3,
                        'chartRank3'     => 1,
                        'chartRank4'     => 2,
                        'chartRank5'     => 2,
                        'pointChart'     => 1,
                        'nbChart'        => 1,
                        'nbChartProven'  => 1,
                    ],
                ],
            ],
        ];
    }
}
