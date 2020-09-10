<?php

namespace VideoGamesRecords\CoreBundle\Tools;

use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;

class Ranking
{
    /**
     * Order an array
     *
     * @param array $array
     * @param array $columns array of 'column' => SORT_*
     *
     * @return array
     */
    public static function order(array $array, array $columns)
    {
        $arrayMultisortParameters = [];
        foreach ($columns as $column => $order) {
            $arrayMultisortParameters[] = array_column($array, $column);
            $arrayMultisortParameters[] = $order;
        }
        $arrayMultisortParameters[] = &$array;
        array_multisort(...$arrayMultisortParameters);

        return $array;
    }

    /**
     * Add a rank column checking one or more columns to a sorted array
     *
     * @param array $array
     * @param string $key
     * @param array $columns
     * @param bool $boolEqual
     *
     * @return array
     */
    public static function addRank($array, $key = 'rank', $columns = ['pointChart'], $boolEqual = false)
    {
        $rank     = 1;
        $compteur = 0;
        $nbEqual  = 1;
        $nb       = count($array);

        for ($i = 0; $i <= $nb - 1; $i++) {
            if ($i >= 1) {
                $row1    = $array[$i - 1];
                $row2    = $array[$i];
                $isEqual = true;
                foreach ($columns as $column) {
                    if ($row1[$column] != $row2[$column]) {
                        $isEqual = false;
                        break;
                    }
                }
                if ($isEqual) {
                    $compteur++;
                    ++$nbEqual;
                } else {
                    $rank     = $rank + $compteur + 1;
                    $compteur = 0;
                    unset($nbEqual);
                    $nbEqual = 1;
                }
            }

            $row       = $array[$i];
            $row[$key] = $rank;
            if ($boolEqual) {
                $row['nbEqual'] = &$nbEqual;
            }
            $array[$i] = $row;
        }
        unset($nbEqual);

        return $array;
    }

    /**
     * @param PlayerChart[] $array
     * @param string $ranking
     * @param array $columns
     *
     * @return array
     */
    public static function addObjectRank($array, $ranking = 'rankPointChart', array $columns = ['pointChart'])
    {
        $setter  = 'set' . ucfirst($ranking);
        $getters = [];
        foreach ($columns as $column) {
            $getters[] = 'get' . ucfirst($column);
        }

        $rank     = 1;
        $compteur = 0;
        $nbEqual  = 1;
        $nb       = count($array);

        for ($i = 0; $i <= $nb - 1; $i++) {
            if ($i >= 1) {
                $object1 = $array[$i - 1];
                $object2 = $array[$i];
                $isEqual = true;
                foreach ($getters as $getter) {
                    if ($object1->$getter() !== $object2->$getter()) {
                        $isEqual = false;
                        break;
                    }
                }
                if ($isEqual) {
                    ++$compteur;
                    ++$nbEqual;
                } else {
                    $rank     = $rank + $compteur + 1;
                    $compteur = 0;
                    $nbEqual  = 1;
                }
            }

            /** @var Player $player */
            $object = $array[$i];
            $object->$setter($rank);
            $array[$i] = $object;
        }

        return $array;
    }

    /**
     * Renvoie le tableau des pointsVGR
     *
     * @param $iNbPartcipant
     *
     * @return mixed
     */
    public static function chartPointProvider($iNbPartcipant)
    {
        $liste       = [];
        $pointRecord = 100 * $iNbPartcipant;
        $nb          = 80;// % différence entre deux positions
        $compteur    = 0;// compteur de position

        // 1er
        $liste[1] = $pointRecord;

        for ($i = 2; $i <= $iNbPartcipant; $i++) {
            $pointRecord = (int)($pointRecord * $nb / 100);
            $liste[$i]   = $pointRecord;
            $compteur++;

            if ($nb < 85) {
                if ($compteur === 2) {
                    $nb++;// le % augmente donc la différence diminue
                    $compteur = 0;
                }
            } elseif ($nb < 99) {
                $nb++;
            }
        }

        return $liste;
    }

    /**
     * @param array $aArray
     * @param array $aBaseCol
     * @param string $sNameNewCol
     * @param string $sColNameToForceZero
     *
     * @return array
     */
    public static function calculateGamePoints($aArray, $aBaseCol, $sNameNewCol, $sColNameToForceZero = '')
    {
        if (empty($aArray)) {
            return $aArray;
        }

        $nameRankCol  = array_shift($aBaseCol);
        $nameEqualCol = array_shift($aBaseCol);

        $nbPlayers     = count($aArray);
        $nbFirstEquals = 1;
        foreach ($aArray as $aRank) {
            if ($aRank[$nameRankCol] == 1) {
                $nbFirstEquals = $aRank[$nameEqualCol];
                break;
            }
        }

        //Get formula to first into ranking
        $a = (-1 / (100 + $nbPlayers - $nbFirstEquals)) + 0.0101 + (log($nbPlayers) / 15000);
        $b = (atan($nbPlayers - 25) + M_PI_2) * (25000 * ($nbPlayers - 25)) / (200 * M_PI);
        $f = ceil((10400000 * $a + $b) / ($nbFirstEquals ** (6 / 5)));

        $aF    = [];
        $aF[1] = $f;
        for ($i = 2; $i <= $nbPlayers; ++$i) {
            $g      = min(0.99, log($i) / (log(71428.6 * $i + 857142.8)) + 0.7);
            $aF[$i] = $aF[$i - 1] * $g;
        }

        for ($i = 0; $i < $nbPlayers; ++$i) {
            //If a column name to force the 0 value is defined, force the 0 value of the new column if the related
            //column value is 0
            if ($sColNameToForceZero !== '' &&
                isset($aArray[$i][$sColNameToForceZero]) &&
                $aArray[$i][$sColNameToForceZero] == 0
            ) {
                $aArray[$i][$sNameNewCol] = 0;
                continue;
            }

            //If firsts
            if ($aArray[$i][$nameRankCol] == 1) {
                $aArray[$i][$sNameNewCol] = (int)round($f, 0);
                continue;
            }
            //If non equals
            if ($aArray[$i][$nameEqualCol] == 1) {
                $aArray[$i][$sNameNewCol] = (int)round($aF[$aArray[$i][$nameRankCol]], 0);
                continue;
            }
            //If equals (do average of players gives if they weren't tied)
            $aTiedValues = [];
            for ($j = 0; $j < $aArray[$i][$nameEqualCol]; ++$j) {
                $aTiedValues[] = $aF[$aArray[$i][$nameRankCol] + $j];
            }
            $value = round(array_sum($aTiedValues) / count($aTiedValues), 0);
            for ($j = $i, $nb = $i + count($aTiedValues); $j < $nb; ++$j) {
                $aArray[$i][$sNameNewCol] = (int)$value;
                $i++;
            }
            $i--;
        }

        return $aArray;
    }
}
