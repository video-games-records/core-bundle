<?php

namespace VideoGamesRecords\CoreBundle\Service\Article;

trait HtmlTopTrait
{
    use HtmlTopPlayerTrait;
    use HtmlTopGameTrait;

    /**
     * @param $row
     * @param $nbGame
     * @return string
     */
    private function diff($row, $nbGame): string
    {
        if ($row['oldRank'] != null) {
            if ($row['rank'] < $row['oldRank']) {
                if ($row['oldRank'] > $nbGame) {
                    $col = '<span class="article-top--new"><abbr title="New">N</abbr></span>';
                } else {
                    $col = sprintf('<span class="article-top--up">+%d <span class="screen-reader-text">position</span></span>', $row['oldRank'] - $row['rank']);
                }
            } elseif ($row['rank'] > $row['oldRank']) {
                $col = sprintf('<span class="article-top--down">-%d <span class="screen-reader-text">position</span></span>', $row['rank'] - $row['oldRank']);
            } else {
                $col = '<span class="article-top--equal"><abbr title="Same position">=</abbr></span>';
            }
        } else {
            $col = '<span class="article-top--new"><abbr title="New">N</abbr></span>';
        }
        return $col;
    }


    /**
     * @return string
     */
    private function getHtmLine(): string
    {
        return '
            <tr>
                <td>%d</td>
                <td>
		            <a href="%s">%s</a>
	            </td>
	            <td>%s posts</td>
	            <td>
	                %s
	            </td>
	        </tr>';
    }

    /**
     * @return string
     */
    private function getHtmlBottom1(): string
    {
        return '
            <tr>
                <td colspan="2" class="article-top__bottom-left">%d - %d</td>
                <td colspan="2" class="article-top__bottom-right">%d posts</td>
            </tr>';
    }

    /**
     * @return string
     */
    private function getHtmlBottom2(): string
    {
        return '
            <tfooter>
                <tr>
                    <th scope="row" colspan="2" class="article-top__bottom-left">Total</th>
                    <td colspan="2" class="article-top__bottom-right">%d posts</td>
                </tr>
            </tfooter>';
    }
}
