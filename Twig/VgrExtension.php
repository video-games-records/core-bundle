<?php
namespace VideoGamesRecords\CoreBundle\Twig;

class VgrExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('vgrFormatScore', array($this, 'formatScoreFunction')),
        );
    }

    /**
     * @param $value
     * @param $mask
     * @return string
     */
    public function formatScoreFunction($value, $mask)
    {
        //----- Parse mask
        $parse = array();
        $arrayParts = explode('|', $mask);
        foreach ($arrayParts as $partOfMask) {
            $arrayLib = explode('~', $partOfMask);
            $parse[] = array('size' => $arrayLib[0], 'suffixe' => $arrayLib[1]);
        }

        //-----
        if ($value == null) {
            return '';
        } else {
            $result = '';
            $localValue = $value;
            $nbElement = count($parse) - 1;
            for ($k = $nbElement; $k >= 0; --$k) {

                $size = $parse[$k]['size'];
                $suffixe = $parse[$k]['suffixe'];
                $lengthLocalValue = strlen($localValue);

                if ($lengthLocalValue > $size) {
                    $tmpValue = substr($localValue, $lengthLocalValue - $size, $size);
                    $localValue = substr($localValue, 0, $lengthLocalValue - $size);
                } elseif ($k != 0) {
                    $tmpValue = str_pad($localValue, $size, '0', STR_PAD_LEFT);
                    $localValue = '';
                } elseif ($lengthLocalValue == 0) {
                    $tmpValue = '0';
                } elseif ($size == 30) {
                    $tmpValue = number_format($localValue);
                } else {
                    $tmpValue = $localValue;
                }

                $result = $tmpValue . $suffixe . $result;

            }
            return $result;
        }
    }

    public function getName()
    {
        return 'vgr_extension';
    }
}