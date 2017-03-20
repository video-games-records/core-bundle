<?php
namespace VideoGamesRecords\CoreBundle\Twig;

class VgrExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('vgrFormatScore', [$this, 'formatScoreFunction']),
            new \Twig_SimpleFunction('vgrRankBgColor', [$this, 'rankBackgroundColor']),
        ];
    }

    /**
     * @param $value
     * @param $mask
     * @return string
     */
    public function formatScoreFunction($value, $mask)
    {
        //----- Parse mask
        $parse = [];
        $arrayParts = explode('|', $mask);
        foreach ($arrayParts as $partOfMask) {
            $arrayLib = explode('~', $partOfMask);
            $parse[] = ['size' => $arrayLib[0], 'suffixe' => $arrayLib[1]];
        }

        //-----
        if ($value === null) {
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

    /**
     * @param $rank
     * @return string
     */
    public function rankBackgroundColor($rank)
    {
        $class = [
            0 => '',
            1 => 'bg-first',
            2 => 'bg-second',
            3 => 'bg-third',
        ];

        if ($rank <= 3) {
            return sprintf("class=\"%s\"", $class[$rank]);
        } else {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vgr_extension';
    }
}
