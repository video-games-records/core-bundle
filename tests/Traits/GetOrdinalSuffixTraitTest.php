<?php

namespace VideoGamesRecords\CoreBundle\Tests\Traits;

use PHPUnit\Framework\TestCase;
use VideoGamesRecords\CoreBundle\Traits\GetOrdinalSuffixTrait;

class GetOrdinalSuffixTraitTest extends TestCase
{
    use GetOrdinalSuffixTrait;

    /**
     * @dataProvider getValues
     * @param int $rank
     * @param string $expected
     */
    public function testGetOrdinalSuffix(int $rank, string $expected)
    {
        $this->assertSame($expected, $this->getOrdinalSuffix($rank));
    }


    public function getValues(): array
    {
        return [
            [1, 'st'],
            [2, 'nd'],
            [3, 'rd'],
            [4, 'th'],
            [11, 'th'],
            [12, 'th'],
            [13, 'th'],
            [21, 'st'],
            [22, 'nd'],
            [23, 'rd'],
        ];
    }
}
