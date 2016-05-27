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
        $result = array();
        $arrayParts = explode('|', $mask);
        foreach ($arrayParts as $partOfMask) {
            $arrayLib = explode('~', $partOfMask);
            $result[] = array('size' => $arrayLib[0], 'suffixe' => $arrayLib[1]);
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
        $data = array();
        for ($k = count($parse) - 1; $k >= 0; $k--) {
            $size = $parse[$k]['size'];
            $suffixe = $parse[$k]['suffixe'];
            $data[] = array('size' => $size, 'suffixe' => $suffixe);
        }
        return array_reverse($data);
    }


    /**
     * Transforme la valeur pour le formulaire
     * @param string $mask
     * @param string $value
     * @return array
     */
    /*public static function getValues($mask, $value)
    {
        $parse = self::parse($mask);
        $data = array();
        $laValue = $value;
        for ($k = count($parse) - 1; $k >= 0; $k--) {

            $taille = $parse[$k]['taille'];
            $suffixe = $parse[$k]['suffixe'];

            if (strlen($laValue) > $taille) {
                $result = substr($laValue, strlen($laValue) - $taille, $taille);
                $laValue = substr($laValue, 0, strlen($laValue) - $taille);
            } else {
                if ($k != 0) {
                    $result = self::_strZero($taille - strlen($laValue)) . $laValue;
                    $laValue = '';
                } else {
                    if (strlen($laValue) == 0) {
                        $result = '0';
                    } else {
                        $result = $laValue;
                    }
                }
            }
            if ($value == null) {
                $result = '';
            }
            $data[] = array('value' => $result);

        }
        return array_reverse($data);
    }*/


    /**
     * @param int $nb
     * @return string
     */
    /*private static function _strZero($nb)
    {
        $string = '';
        for ($i = 1; $i <= $nb; $i++) {
            $string .= '0';
        }
        return $string;
    }*/


    /**
     * Transforme la valeur pour insertion an base
     * @param string $mask
     * @param array $values
     * @return string
     */
    /*public static function formToBdd($mask, $values)
    {
        $parse = self::parse($mask);
        $nbChamp = count($parse);

        $value = implode('', $values);
        if ($value == '') {
            return null;
        } else if ($nbChamp == 1) {
            return $value;
        } else {
            $value = '';
            for ($k = 0; $k <= $nbChamp - 1; $k++) {
                $part = $values[$k];
                $length = $parse[$k]['taille'];
                if (strlen($part) < $length) {
                    if ($k == 0) {
                        if ($part == '') {
                            $part = '0';
                        }
                    } else {
                        if ($k == $nbChamp - 1) {
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
    }*/


}