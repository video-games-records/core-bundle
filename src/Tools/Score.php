<?php

namespace VideoGamesRecords\CoreBundle\Tools;

class Score
{
    /**
     * Parse a type of a libRecord
     * @param string $mask
     * @return array
     */
    public static function parseChartMask(string $mask): array
    {
        $result     = [];
        $arrayParts = explode('|', $mask);
        foreach ($arrayParts as $partOfMask) {
            $arrayLib = explode('~', $partOfMask);
            $result[] = ['size' => (int) $arrayLib[0], 'suffixe' => $arrayLib[1]];
        }

        return $result;
    }

    /**
     * Transform a value for the form
     * @param string     $mask
     * @param string|int $value
     * @return array
     */
    public static function getValues(string $mask, $value): array
    {
        $parse   = self::parseChartMask($mask);
        $negative = str_starts_with($value, '-');
        $value = $negative ? (int) substr($value, 1) : $value;
        $data    = [];
        $laValue = $value;
        for ($k = count($parse) - 1; $k >= 0; $k--) {
            $size = $parse[$k]['size'];

            if (strlen($laValue) > $size) {
                $result  = substr($laValue, strlen($laValue) - $size, $size);
                $laValue = substr($laValue, 0, strlen($laValue) - $size);
            } else {
                if ($k !== 0) {
                    $result  = str_pad($laValue, $size, '0', STR_PAD_LEFT);
                    $laValue = '';
                } else {
                    $result = $laValue;
                    if ('' === $laValue) {
                        $result = '0';
                    }
                    if ($negative) {
                        $result = '-' . $result;
                    }
                }
            }
            if ($value === null) {
                $result = '';
            }
            $data[] = ['value' => $result];
        }

        return array_reverse($data);
    }

    /**
     * Transform values to insert database
     * @param string $mask
     * @param array  $values
     * @return string|null
     */
    public static function formToBdd(string $mask, array $values): ?string
    {
        $parse   = self::parseChartMask($mask);
        $nbInput = count($parse);
        $value = '';
        foreach ($values as $row) {
            $value .= $row['value'];
        }
        if ($value == '') {
            return null;
        }
        if ($nbInput === 1) {
            return $values[0]['value'];
        }
        $value = '';
        for ($k = 0; $k <= $nbInput - 1; $k++) {
            $part   = $values[$k]['value'];
            $length = $parse[$k]['size'];
            if (strlen($part) < $length) {
                if ($k === 0) {
                    if ($part === '') {
                        $part = '0';
                    }
                } else {
                    if ($k === $nbInput - 1) {
                        $part = str_pad($part, $length, '0', STR_PAD_RIGHT);
                    } else {
                        $part = str_pad($part, $length, '0', STR_PAD_LEFT);
                    }
                }
            }
            $value .= $part;
        }

        return $value;
    }

    /**
     * @param        $value
     * @param string $mask
     * @return string
     */
    public static function formatScore($value, string $mask): string
    {
        $parse = self::parseChartMask($mask);

        if ($value === null) {
            return '';
        }

        $result     = '';
        $negative = str_starts_with($value, '-');
        $localValue = $negative ? (int) substr($value, 1) : $value;
        $nbElement  = count($parse) - 1;
        for ($k = $nbElement; $k >= 0; --$k) {
            $size             = $parse[$k]['size'];
            $suffixe          = $parse[$k]['suffixe'];
            $lengthLocalValue = strlen($localValue);

            if ($lengthLocalValue > $size) {
                $tmpValue   = substr($localValue, $lengthLocalValue - $size, $size);
                $localValue = substr($localValue, 0, $lengthLocalValue - $size);
            } elseif ($k !== 0) {
                $tmpValue   = str_pad($localValue, $size, '0', STR_PAD_LEFT);
                $localValue = '';
            } elseif ($lengthLocalValue === 0) {
                $tmpValue = '0';
            } elseif ($size === 30) {
                $tmpValue = number_format($localValue);
            } else {
                $tmpValue = $localValue;
            }

            $result = $tmpValue . $suffixe . $result;
        }

        return ($negative ? '-' : '') .  $result;
    }
}
