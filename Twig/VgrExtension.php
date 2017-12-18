<?php
namespace VideoGamesRecords\CoreBundle\Twig;

use VideoGamesRecords\CoreBundle\Tools\Score;

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
        return Score::formatScore($value, $mask);
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
