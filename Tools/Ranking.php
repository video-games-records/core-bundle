<?php

namespace VideoGamesRecords\CoreBundle\Tools;

class Ranking
{

    /**
     * Order an array
     * @param array $array
     * @param array $columns
     * @return array
     */
    public static function order($array, $columns)
    {
        $nb = count($array);
        for ($i=0; $i<=$nb; $i++) {
            $change = false;
            for ($j=0; $j<$nb-2; $j++) {
                $row1 = $array[$j];
                $row2 = $array[$j+1];
                foreach ($columns as $column => $order) {
                    if ( (($order == 'ASC') && ($row1[$column] < $row2[$column])) || (($order == 'DESC') && ($row1[$column] > $row2[$column])) ) {
                        break;
                    } else if ( (($order == 'ASC') && ($row1[$column] > $row2[$column])) || (($order == 'DESC') && ($row1[$column] < $row2[$column])) ) {
                        $array[$j] = $row2;
                        $array[$j+1] = $row1;
                        $change = true;
                        break;
                    }
                }
            }
            if ($change == false) {
                break;
            }
        }
        return $array;
    }

    /**
     * Add a rank column checking one or more columns to a sort array
     *
     * @param array $array
     * @param string $key
     * @param array $columns
     * @param bool $boolEqual
     *
     * @return array
     */
    public static function addRank($array, $key = 'rank', $columns = array('pointRecord'), $boolEqual = false)
    {
        $rank = 1;
        $compteur = 0;
        $nbEqual = 1;
        $nb = count($array);

        for ($i=0; $i<=$nb-1; $i++) {

            if ($i >= 1) {
                $row1 = $array[$i-1];
                $row2 = $array[$i];
                $isEqual = true;
                foreach ($columns as $column) {
                    if ($row1[$column] != $row2[$column]) {
                        $isEqual = false;
                        break;
                    }
                }
                if ($isEqual) {
                    $compteur++;
                    $nbEqual = $nbEqual + 1;
                } else {
                    $rank = $rank + $compteur + 1;
                    $compteur = 0;
                    unset($nbEqual);
                    $nbEqual = 1;
                }
            }

            $row = $array[$i];
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
     * Renvoie le tableau des pointsVGR
     * @param $iNbPartcipant
     * @return mixed
     */
    public static function arrayPointRecord($iNbPartcipant)
    {
        if ($iNbPartcipant > 200) {
            $p = 200;
        } else {
            $p = $iNbPartcipant;
        }

        $pointRecord = 100 * $p;
        $nb = 80;// % différence entre deux positions
        $compteur = 0;// compteur de position

        // 1er
        $liste[1] = $pointRecord;

        for ($i=2; $i<=$p; $i++) {
            $pointRecord = intval($pointRecord * $nb/100);
            $liste[$i] = $pointRecord;
            $compteur++;

            if ($nb < 85) {
                if ($compteur == 2) {
                    $nb++;// le % augmente donc la différence diminue
                    $compteur = 0;
                }
            } else if ($nb < 99) {
                $nb++;
            }
        }

        if ($iNbPartcipant > 200) {
            for ($i=201;$i<=$iNbPartcipant;$i++) {
                $liste[$i] = 0;
            }
        }

        return $liste;
    }


    /**
     * @param $iNbPartcipant
     * @return mixed
     */
    public static function arrayPointRecord2($iNbPartcipant)
    {
        if ($iNbPartcipant > 1000) {
            $p = 1000;
        } else {
            $p = $iNbPartcipant;
        }

        $pointRecord = 1000 * pow($p, 1.1);
        $nb = 80;// % différence entre deux positions
        $maxPercent= 97;

        // 1er
        $liste[1] = $pointRecord;
        for ($i=2;$i<=$p;$i++) {
            $pointRecord = $pointRecord * $nb/100.0;
            $liste[$i] = $pointRecord;
            $nb += ((100-$nb)/20.0);
            if ($nb > $maxPercent ) { //97.5 semble optimal
                $nb = $maxPercent;
                $maxPercent += (100-$maxPercent)/80.0;
                $maxPercent = min(99, $maxPercent);
            }
        }
        if ($iNbPartcipant > 1000) {
            for ($i=1001;$i<=$iNbPartcipant;$i++) {
                $liste[$i] = 0;
            }
        }

        foreach ($liste as &$elt) {
            $elt = floor($elt);
        } unset($elt);

        return $liste;
    }

    /**
     * Calcule la somme des éléments d'indice désiré dans un tableau 2D
     * Renvoi 0 si le tableau est vide
     * Les éléments non numériques et non présents sont ignorés
     * @param $aArray
     * @param $sKey
     * @return int
     */
    public static function arraySumOn2Dkey($aArray, $sKey)
    {
        $iValue = 0;
        if (empty($aArray)) {
            return 0;
        }
        foreach ($aArray as $aElements) {
            if (isset($aElements[$sKey]) && is_numeric($aElements[$sKey])) {
                $iValue += $aElements[$sKey];
            }
        }
        return $iValue;
    }

}
