<?php

namespace VideoGamesRecords\CoreBundle\Tools;

class Score
{
    /**
     * Parse a type of a libRecord
     * @param string $mask
     * @return array
     */
    public static function parse($mask)
    {
        $result = [];
        $arrayParts = explode('|', $mask);
        foreach ($arrayParts as $partOfMask) {
            $arrayLib = explode('~', $partOfMask);
            $result[] = ['size' => $arrayLib[0], 'suffixe' => $arrayLib[1]];
        }

        return $result;
    }

    /**
     * Return the data to create imput field
     * @param string $mask
     * @return array
     */
    public static function getInputs($mask)
    {
        $parse = self::parse($mask);
        $data = [];
        for ($k = count($parse) - 1; $k >= 0; $k--) {
            $size = $parse[$k]['size'];
            $suffixe = $parse[$k]['suffixe'];
            $data[] = ['size' => $size, 'suffixe' => $suffixe];
        }
        return array_reverse($data);
    }


    /**
     * Transform a value for the form
     * @param string $mask
     * @param string $value
     * @return array
     */
    public static function getValues($mask, $value)
    {
        $parse = self::parse($mask);
        $data = [];
        $laValue = $value;
        for ($k = count($parse) - 1; $k >= 0; $k--) {
            $size = $parse[$k]['size'];

            if (strlen($laValue) > $size) {
                $result = substr($laValue, strlen($laValue) - $size, $size);
                $laValue = substr($laValue, 0, strlen($laValue) - $size);
            } else {
                if ($k != 0) {
                    $result = str_pad($laValue, $size - strlen($laValue), '0', STR_PAD_LEFT);
                    $laValue = '';
                } else {
                    if (strlen($laValue) === 0) {
                        $result = '0';
                    } else {
                        $result = $laValue;
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
     * @param array $values
     * @return string
     */
    public static function formToBdd($mask, $values)
    {
        $parse = self::parse($mask);
        $nbInput = count($parse);

        $value = implode('', $values);
        if ($value == '') {
            return null;
        } else if ($nbInput == 1) {
            return $value;
        } else {
            $value = '';
            for ($k = 0; $k <= $nbInput - 1; $k++) {
                $part = $values[$k];
                $length = $parse[$k]['size'];
                if (strlen($part) < $length) {
                    if ($k == 0) {
                        if ($part == '') {
                            $part = '0';
                        }
                    } else {
                        if ($k == $nbInput - 1) {
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
    }
}
