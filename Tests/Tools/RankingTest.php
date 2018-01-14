<?php

namespace Tests\VideoGamesRecords\CoreBundle\Tests\Tools;

use PHPUnit\Framework\TestCase;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class RankingTest extends TestCase
{
    /**
     * @dataProvider arrayPointRecordProvider
     *
     * @param int $nbParticipant
     */
    public function testChartPointProvider($nbParticipant)
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
        $list = Ranking::calculateGamePoints($list, ['rankPointChart', 'nbEqual'], 'pointGame', 'pointChart');
        foreach ($list as $ranking) {
            $this->assertArrayHasKey('pointGame', $ranking);
        }
    }

    /**
     * @dataProvider listSortedByPointChartProvider
     *
     * @param array $list
     * @param array $expected
     */
    public function testAddRank(array $list, array $expected)
    {
        $list = Ranking::addRank($list, 'rankPointChart', ['pointChart'], true);
        foreach ($list as $key => $ranking) {
            $this->assertSame($expected[$key], $ranking);
        }
    }

    /**
     * @dataProvider objectSortedByPointChartProvider
     *
     * @param array $list
     * @param array $expected
     */
    public function testAddObjectRank(array $list, array $expected)
    {
        $list = Ranking::addObjectRank($list, 'rankPointChart', ['pointChart']);
        foreach ($list as $key => $ranking) {
            $this->assertSame($expected[$key]['rank'], $ranking->getRankPointChart());
        }
    }

    /**
     * @dataProvider orderProvider
     *
     * @param array $list
     * @param array $expected
     * @param array $sorting
     */
    public function testOrder(array $list, array $expected, array $sorting)
    {
        $list = Ranking::order($list, $sorting);
        $this->assertSame($list, $expected);
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
                        'idPlayer'      => 2,
                        'idGame'        => 1,
                        'rankMedal'     => 1,
                        'chartRank0'    => 1,
                        'chartRank1'    => 2,
                        'chartRank2'    => 3,
                        'chartRank3'    => 1,
                        'chartRank4'    => 2,
                        'chartRank5'    => 2,
                        'pointChart'    => 10,
                        'nbChart'       => 1,
                        'nbChartProven' => 1,
                    ],
                    [
                        'idPlayer'      => 1,
                        'idGame'        => 1,
                        'rankMedal'     => 1,
                        'chartRank0'    => 1,
                        'chartRank1'    => 2,
                        'chartRank2'    => 3,
                        'chartRank3'    => 1,
                        'chartRank4'    => 2,
                        'chartRank5'    => 3,
                        'pointChart'    => 1,
                        'nbChart'       => 1,
                        'nbChartProven' => 1,
                    ],
                ],
            ],
        ];
    }

    public function listSortedByPointChartProvider()
    {
        return [
            [
                // List sorted
                [
                    [
                        'idPlayer'      => 2,
                        'idGame'        => 1,
                        'rankMedal'     => 1,
                        'chartRank0'    => 1,
                        'chartRank1'    => 2,
                        'chartRank2'    => 3,
                        'chartRank3'    => 1,
                        'chartRank4'    => 2,
                        'chartRank5'    => 2,
                        'pointChart'    => 10,
                        'nbChart'       => 1,
                        'nbChartProven' => 1,
                    ],
                    [
                        'idPlayer'      => 1,
                        'idGame'        => 1,
                        'rankMedal'     => 1,
                        'chartRank0'    => 1,
                        'chartRank1'    => 2,
                        'chartRank2'    => 3,
                        'chartRank3'    => 1,
                        'chartRank4'    => 2,
                        'chartRank5'    => 3,
                        'pointChart'    => 1,
                        'nbChart'       => 1,
                        'nbChartProven' => 1,
                    ],
                    [
                        'idPlayer'      => 1,
                        'idGame'        => 1,
                        'rankMedal'     => 1,
                        'chartRank0'    => 1,
                        'chartRank1'    => 2,
                        'chartRank2'    => 3,
                        'chartRank3'    => 1,
                        'chartRank4'    => 2,
                        'chartRank5'    => 3,
                        'pointChart'    => 1,
                        'nbChart'       => 1,
                        'nbChartProven' => 1,
                    ],
                ],
                // Expected
                [
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
                        'pointChart'     => 10,
                        'nbChart'        => 1,
                        'nbChartProven'  => 1,
                        'rankPointChart' => 1,
                        'nbEqual'        => 1,
                    ],
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
                        'rankPointChart' => 2,
                        'nbEqual'        => 2,
                    ],
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
                        'rankPointChart' => 2,
                        'nbEqual'        => 2,
                    ],
                ],
            ],
        ];
    }

    public function objectSortedByPointChartProvider()
    {
        return [
            [
                // List sorted
                [
                    (new Player())->setPointChart(100),
                    (new Player())->setPointChart(10),
                    (new Player())->setPointChart(10),
                ],
                // Expected
                [
                    ['rank' => 1],
                    ['rank' => 2],
                    ['rank' => 2],
                ],
            ],
        ];
    }

    public function orderProvider()
    {
        return [
            [
                // actual
                [
                    ['id' => 0, 'pointChart' => 100],
                    ['id' => 1, 'pointChart' => 1],
                    ['id' => 2, 'pointChart' => 10],
                ],
                // expected
                [
                    ['id' => 0, 'pointChart' => 100],
                    ['id' => 2, 'pointChart' => 10],
                    ['id' => 1, 'pointChart' => 1],
                ],
                // sorting
                [
                    'pointChart' => SORT_DESC,
                ],
            ],
            [
                // actual
                [
                    ['id' => 0, 'rank0' => 100, 'rank1' => 10, 'rank2' => 1, 'rank3' => 0],
                    ['id' => 1, 'rank0' => 10, 'rank1' => 10, 'rank2' => 1, 'rank3' => 0],
                    ['id' => 2, 'rank0' => 100, 'rank1' => 9, 'rank2' => 1, 'rank3' => 0],
                ],
                // expected
                [
                    ['id' => 0, 'rank0' => 100, 'rank1' => 10, 'rank2' => 1, 'rank3' => 0],
                    ['id' => 2, 'rank0' => 100, 'rank1' => 9, 'rank2' => 1, 'rank3' => 0],
                    ['id' => 1, 'rank0' => 10, 'rank1' => 10, 'rank2' => 1, 'rank3' => 0],
                ],
                // sorting
                [
                    'rank0' => SORT_DESC,
                    'rank1' => SORT_DESC,
                    'rank2' => SORT_DESC,
                    'rank3' => SORT_DESC,
                ],
            ],
        ];
    }
}
