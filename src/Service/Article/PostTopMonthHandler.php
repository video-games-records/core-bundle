<?php
namespace VideoGamesRecords\CoreBundle\Service\Article;

use DateInterval;
use DateTime;
use Exception;
use VideoGamesRecords\DwhBundle\Service\TopGameProvider;
use VideoGamesRecords\DwhBundle\Service\TopPlayerProvider;
use ProjetNormandie\ArticleBundle\Service\Writer;

class PostTopMonthHandler implements PostTopHandlerInterface
{
    use HtmlTopTrait;

    private TopGameProvider $topGameProvider;
    private TopPlayerProvider $topPlayerProvider;
    private Writer $writer;

    public function __construct(
        TopGameProvider $topGameProvider,
        TopPlayerProvider $topPlayerProvider,
        Writer $writer
    ) {
        $this->topGameProvider = $topGameProvider;
        $this->topPlayerProvider = $topPlayerProvider;
        $this->writer = $writer;
    }


    /**
     * @param $day
     * @throws Exception
     */
    public function handle($day): void
    {
        $date1Begin = new DateTime($day);
        $date1End = new DateTime($day);

        $date1End->sub(new DateInterval('P1D'));
        $date1Begin->sub(new DateInterval('P1M'));

        $date2Begin = clone($date1Begin);
        $date2End = clone($date1End);

        $date2Begin->sub(new DateInterval('P1M'));
        $date2End->sub(new DateInterval('P1M'));

        $month = $date1Begin->format('F');

        $gamesData = $this->topGameProvider->getTop($date1Begin, $date1End, $date2Begin, $date2End, 50);
        $gamesHtmlEn = $this->getHtmlTopGame($gamesData, 'en');
        $gamesHtmlFr = $this->getHtmlTopGame($gamesData, 'fr');

        $playersData = $this->topPlayerProvider->getTop($date1Begin, $date1End, $date2Begin, $date2End, 50);
        $playersHtmlEn = $this->getHtmlTopPlayer($playersData, 'en');
        $playersHtmlFr = $this->getHtmlTopPlayer($playersData, 'fr');

        $textEn = $gamesHtmlEn . '<br /><br />' . $playersHtmlEn;
        $textFr = $gamesHtmlFr . '<br /><br />' . $playersHtmlFr;

        $this->writer->write(
            array(
                'en' => 'Top of month #' . $month,
                'fr' => 'Top du mois #' . $month,
            ),
            array(
                'en' => $textEn,
                'fr' => $textFr,
            )
        );
    }
}