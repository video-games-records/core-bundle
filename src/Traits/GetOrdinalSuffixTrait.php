<?php

namespace VideoGamesRecords\CoreBundle\Traits;

trait GetOrdinalSuffixTrait
{
    /**
     * @param $number
     * @return string
     */
    private function getOrdinalSuffix($number): string
    {
        if ($number <= 0) {
            return '';
        }
        $number %= 100;
        if ($number != 11 && ($number % 10) == 1) {
            return 'st';
        }
        if ($number != 12 && ($number % 10) == 2) {
            return 'nd';
        }
        if ($number != 13 && ($number % 10) == 3) {
            return 'rd';
        }
        return 'th';
    }
}
