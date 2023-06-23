<?php

namespace VideoGamesRecords\CoreBundle\Traits;

trait NumberFormatTrait
{
    /**
     * @param $value
     * @return string
     */
    private function numberFormat($value): string
    {
        return number_format($value);
    }
}
