<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\File\Picture;

/**
 * Class GamercardController
 * @Route("/gamercard")
 * @Cache(expires="tomorrow", public=true)
 */
class GamercardController extends AbstractController
{
    /**
     * @Route("/mini/{id}", name="gamercard_mini", methods={"GET"})
     * @Cache(smaxage="900")
     * @param Player $player
     * @ParamConverter("player", class="VideoGamesRecordsCoreBundle:Player")
     * @throws Exception
     */
    public function miniAction(Player $player)
    {
        $directory = $this->getParameter('videogamesrecords_core.directory.picture');

        chdir(__DIR__);
        $gamercard = Picture::loadFile('../Resources/img/gamercard/mini.png', true);

        // Ranking Points
        $fontSize = 8;
        $gamercard
            ->addColor('lightBrown', 255, 218, 176)
            ->addFont('segoeUISemiBold', '../Resources/fonts/seguisb.ttf')
            ->write($this->numberFormat($player->getPointGame()) . ' Pts', $fontSize, 40, 20)
            ->write('/', $fontSize, 124, 20)
            ->addColor('darkYellow', 255, 191, 1)
            ->write($player->getRankPointGame() . ' ' . $this->getOrdinalSuffix($player->getRankPointGame()), $fontSize, 130, 20);


        // Ranking Medals
        $sprite = Picture::loadFile('../Resources/img/sprite.png');
        $gamercard
            ->copyResized($sprite, 164, 8, 126, 160, 16, 16, 16, 16) //Platinium
            ->copyResized($sprite, 211, 8, 108, 160, 16, 16, 16, 16)       //Gold
            ->copyResized($sprite, 258, 8, 92, 160, 16, 16, 16, 16)       //Silver
            ->copyResized($sprite, 305, 8, 74, 160, 16, 16, 16, 16);      //Bronze

        $gamercard->getColor('lightBrown');
        $gamercard
            ->write($player->getChartRank0(), $fontSize, 180, 20)
            ->write($player->getChartRank1(), $fontSize, 227, 20)
            ->write($player->getChartRank2(), $fontSize, 274, 20)
            ->write($player->getChartRank3(), $fontSize, 321, 20);
        $gamercard->write('/', $fontSize, 350, 20);
        $gamercard->getColor('darkYellow');
        $rank = $player->getRankMedal();
        if ($rank <= 99) {
            $rank .= $this->getOrdinalSuffix($rank);
        }
        $gamercard->write($rank, $fontSize, 356, 20);

        // Add avatar
        try {
            $avatar = Picture::loadFile($directory . 'user/' . $player->getAvatar(), true);
        } catch (Exception $e) {
            $avatar = Picture::loadFile($directory . 'avatar/default.png', true);
        }
        $gamercard->copyResized($avatar, 4, 2, 0, 0, 26, 26);

        try {
            $gamercard->downloadPicture('png', 'VGR-GamerCard-Mini-' . $player->getSlug() . '.png');
        } catch (Exception $e) {
            exit;
        }
        exit;
    }

    /**
     * @Route("/classic/{id}", name="gamercard_classic", methods={"GET"})
     * @Cache(smaxage="900")
     * @param Player $player
     * @ParamConverter("player", class="VideoGamesRecordsCoreBundle:Player")
     * @throws Exception
     */
    public function classicAction(Player $player)
    {
        $directory = $this->getParameter('videogamesrecords_core.directory.picture');
        chdir(__DIR__);

        $gamercard = new Picture(210, 135);
        $gamercard->addColor('black', 13, 14, 15)
            ->addRectangle(0, 0, 210, 24)
            ->addColor('grey', 58, 56, 56)
            ->addRectangle(0, 25, 210, 135)
            ->addColor('lightGrayLine', 196, 196, 196)
            ->addRectangle(78, 52, 204, 53)
            ->addColor('darkGrayLine', 86, 86, 86)
            ->addRectangle(78, 52, 203, 52);

        // Pseudo
        if ($player->getTeam() !== null) {
            $pseudo = sprintf('[%s] %s', $player->getTeam()->getTag(), $player->getPseudo());
        } else {
            $pseudo = $player->getPseudo();
        }
        $gamercard->addColor('orange', 246, 162, 83)
            ->addFont('segoeUILight', '../Resources/fonts/segoeuil.ttf')
            ->write($pseudo, 12.375, 9, 17);

        // Ranking
        $fontSize = 7.5;
        $rankMedal = '/' . $player->getRankMedal();
        if ($player->getRankMedal() <= 999) {
            $rankMedal .= $this->getOrdinalSuffix($player->getRankMedal());
        }
        $pointGame = $this->numberFormat($player->getPointGame()) . ' Pts / ';
        $pointGame .= $player->getRankPointGame() . $this->getOrdinalSuffix($player->getRankPointGame());
        $gamercard
            ->addColor('white', 255, 255, 255)
            ->addFont('segoeUISemiBold', '../Resources/fonts/seguisb.ttf')
            ->write($player->getChartRank0(), $fontSize, 96, 70)
            ->write($player->getChartRank1(), $fontSize, 145, 70)
            ->write($player->getChartRank2(), $fontSize, 96, 90)
            ->write($player->getChartRank3(), $fontSize, 145, 90)
            ->write($rankMedal, $fontSize, 175, 80)
            ->write($pointGame, $fontSize, 82, 45);

        // Add sprites pictures medals
        $sprite = Picture::loadFile('../Resources/img/sprite.png');
        $gamercard
            ->copyResized($sprite, 78, 59, 126, 160, 16, 16, 16, 16) //Platinium
            ->copyResized($sprite, 127, 59, 108, 160, 16, 16, 16, 16)       //Gold
            ->copyResized($sprite, 78, 79, 92, 160, 16, 16, 16, 16)       //Silver
            ->copyResized($sprite, 127, 79, 74, 160, 16, 16, 16, 16);      //Bronze

        // Add avatar
        try {
            $avatar = Picture::loadFile($directory . 'user/' . $player->getAvatar(), true);
        } catch (Exception $e) {
            $avatar = Picture::loadFile($directory . 'avatar/default.png', true);
        }
        $gamercard->copyResized($avatar, 9, 30, 0, 0, 64, 64);

        $playerGames = $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerGame')->findBy(
            array(
                'player' => $player
            ),
            array('lastUpdate' => 'DESC'),
            5
        );

        $startX = 9;
        foreach ($playerGames as $playerGame) {
            $picture = Picture::loadFile($directory . 'badge/' . $playerGame->getGame()->getBadge()->getPicture());
            $gamercard->copyResized($picture, $startX, 99);
            $startX += 38;
        }


        try {
            $gamercard->downloadPicture('png', 'VGR-GamerCard-Classic-' . $player->getSlug() . '.png');
        } catch (Exception $e) {
            exit;
        }
        exit;
    }

    /**
     * @param $number
     * @return string
     */
    private function getOrdinalSuffix($number): string
    {
        if ($number <= 0) {
            return '';
        }
        $number %= 100;
        if ($number != 11 && ($number % 10) == 1) {
            return 'st';
        }
        if ($number != 12 && ($number % 10) == 2) {
            return 'nd';
        }
        if ($number != 13 && ($number % 10) == 3) {
            return 'rd';
        }
        return 'th';
    }

    /**
     * @param      $value
     * @return string
     */
    private function numberFormat($value): string
    {
        return number_format($value);
    }
}
