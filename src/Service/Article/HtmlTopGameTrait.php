<?php

namespace VideoGamesRecords\CoreBundle\Service\Article;

trait HtmlTopGameTrait
{
    /**
     * @param        $data
     * @param string $locale
     * @return string
     */
    private function getHtmlTopGame($data, string $locale = 'en'): string
    {
        $html = '';

        if (count($data['list']) > 0) {
            $html .= '<div class="article-top article-top__games">';

            for ($i = 0; $i <= 2; $i++) {
                if (array_key_exists($i, $data['list'])) {
                    $html .= sprintf(
                        '<a href="%s"><img src="%s" alt="%s" class="article-top__game" /></a>',
                        '/' . $locale . '/' . $data['list'][$i]['game']->getUrl(),
                        'https://picture.video-games-records.com/game/' . $data['list'][$i]['game']->getPicture(),
                        $data['list'][$i]['game']->getName()
                    );
                }
                if ($i == 0) {
                    $html .= '<br />';
                }
            }

            $html .= '<table class="article-top__table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th scope="col"><abbr title="Rank">#</abbr></th>';
            $html .= '<th scope="col">Game</th>';
            $html .= '<th scope="col">Posts submitted</th>';
            $html .= '<th scope="col">Position change</th>';
            $html .= '</tr>';
            $html .= '</tr>';
            $html .= '<tbody>';

            foreach ($data['list'] as $row) {
                $html .= sprintf(
                    $this->getHtmLine(),
                    $row['rank'],
                    '/' . $locale . '/' . $row['game']->getUrl(),
                    $row['game']->getName(),
                    $row['nb'],
                    $this->diff($row, count($data['list']))
                );
            }
            if ($data['nbTotalPost'] > $data['nbPostFromList']) {
                $html .= sprintf(
                    $this->getHtmlBottom1(),
                    count($data['list']) + 1,
                    $data['nbItem'],
                    $data['nbTotalPost'] - $data['nbPostFromList']
                );
            }
            $html .= sprintf($this->getHtmlBottom2(), $data['nbTotalPost']);
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        return $html;
    }
}
