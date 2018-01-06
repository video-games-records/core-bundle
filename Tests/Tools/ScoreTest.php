<?php

namespace Tests\VideoGamesRecords\CoreBundle\Tests\Tools;

use PHPUnit\Framework\TestCase;
use VideoGamesRecords\CoreBundle\Tools\Score;

class ScoreTest extends TestCase
{
    /**
     * @dataProvider parseProvider
     * @covers       Score::parseChartMask()
     *
     * @param string $scoreFormat
     * @param array $expected
     */
    public function testParseLibRecord($scoreFormat, array $expected)
    {
        $this->assertSame($expected, Score::parseChartMask($scoreFormat));
    }

    /**
     * @dataProvider getValuesProvider
     *
     * @param string $scoreFormat
     * @param int $value
     * @param array $expected
     */
    public function testGetValues($scoreFormat, $value, array $expected)
    {
        $this->assertSame($expected, Score::getValues($scoreFormat, $value));
    }

    /**
     * @dataProvider formToBddProvider
     *
     * @param string $scoreFormat
     * @param array $values
     * @param string $expected
     */
    public function testFormToBdd($scoreFormat, array $values, $expected)
    {
        $this->assertSame($expected, Score::formToBdd($scoreFormat, $values));
    }

    /**
     * @dataProvider formatScoreProvider
     * @param string $scoreFormat
     * @param string $value
     * @param string $expected
     */
    public function testFormatScore($scoreFormat, $value, $expected)
    {
        $this->assertSame($expected, Score::formatScore($value, $scoreFormat));
    }

    public function parseProvider()
    {
        return [
            ['30~,|2~ kg', [['size' => 30, 'suffixe' => ','], ['size' => 2, 'suffixe' => ' kg']]],
            ['30~,|2~', [['size' => 30, 'suffixe' => ','], ['size' => 2, 'suffixe' => '']]],
            ['30~ yd', [['size' => 30, 'suffixe' => ' yd']]],
            ['30~ km |3~ m |2~ cm |1~ mm', [['size' => 30, 'suffixe' => ' km '], ['size' => 3, 'suffixe' => ' m '], ['size' => 2, 'suffixe' => ' cm '], ['size' => 1, 'suffixe' => ' mm'],]],
        ];
    }

    public function getValuesProvider()
    {
        return [
            ['30~,|2~ kg', 1100, [['value' => '11'], ['value' => '00']]],
            ['30~,|5~', 1100, [['value' => '0'], ['value' => '01100']]],
            ['30~', 1100, [['value' => 1100]]],
            ['30~h|2~mn|2~s', -1100, [['value' => '-0'], ['value' => '11'], ['value' => '00']]],
            ['30~h|2~mn|2~s', -100, [['value' => '-0'], ['value' => '01'], ['value' => '00']]],
            ['30~h|1~mn|2~s', -100, [['value' => '-0'], ['value' => '1'], ['value' => '00']]],
        ];
    }

    public function formToBddProvider()
    {
        return [
            ['30~,|2~ kg', ['11', '0'], '1100'],
            ['30~,|2~ kg', ['-11', '0'], '-1100'],
            ['30~,|2~ kg', [], null],
            ['30~,|2~ kg', ['', ''], null],
            ['30~ yd', ['10'], '10'],
        ];
    }

    public function formatScoreProvider()
    {
        return [
            ['30~,|2~ kg', 1100, '11,00 kg'],
            ['30~ yd', 10, '10 yd'],
            ['30~ yd', null, ''],
            ['30~h|2~mn|2~s', -111014, '-11h10mn14s'],
        ];
    }
}
