<?php

namespace VideoGamesRecords\CoreBundle\Controller\Player\Gamercard;

use Exception;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\File\PictureCreatorFactory;
use VideoGamesRecords\CoreBundle\Traits\GetOrdinalSuffixTrait;
use VideoGamesRecords\CoreBundle\Traits\NumberFormatTrait;

/**
 * @Route("/gamercard")
 * @Cache(expires="tomorrow", public=true)
 */
class Mini extends AbstractController
{
    use GetOrdinalSuffixTrait;
    use NumberFormatTrait;

    private FilesystemOperator $appStorage;

    public function __construct(FilesystemOperator $appStorage) {
        $this->appStorage = $appStorage;
    }

    /**
     * @Route("/mini/{id}", name="gamercard_mini_1", methods={"GET"})
     * @Route("/{id}/mini", name="gamercard_mini_2", methods={"GET"})
     * @Cache(smaxage="900")
     * @param Player $player
     * @throws Exception
     * @throws FilesystemException
     */
    public function __invoke(Player $player): void
    {
        chdir(__DIR__);
        $gamercard = PictureCreatorFactory::fromFile('../../../Resources/img/gamercard/mini.png');

        // Ranking Points
        $fontSize = 8;
        $gamercard
            ->addColor('lightBrown', 255, 218, 176)
            ->addFont('segoeUISemiBold', '../../../Resources/fonts/seguisb.ttf')
            ->write($this->numberFormat($player->getPointGame()) . ' Pts', $fontSize, 40, 20)
            ->write('/', $fontSize, 124, 20)
            ->addColor('darkYellow', 255, 191, 1)
            ->write($player->getRankPointGame() . ' ' . $this->getOrdinalSuffix($player->getRankPointGame()), $fontSize, 130, 20);


        // Ranking Medals
        $sprite = PictureCreatorFactory::fromFile('../../../Resources/img/sprite.png');
        $gamercard
            ->copyResized($sprite, 164, 8, 126, 160, 16, 16, 16, 16)
            ->copyResized($sprite, 211, 8, 108, 160, 16, 16, 16, 16)
            ->copyResized($sprite, 258, 8, 92, 160, 16, 16, 16, 16)
            ->copyResized($sprite, 305, 8, 74, 160, 16, 16, 16, 16);

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
        $avatar = PictureCreatorFactory::fromStream($this->getAvatar($player));
        $gamercard->copyResized($avatar, 4, 2, 0, 0, 26, 26);

        try {
            $gamercard->downloadPicture('png', 'VGR-GamerCard-Mini-' . $player->getSlug() . '.png');
        } catch (Exception $e) {
            exit;
        }
        exit;
    }


    /**
     * @param      $value
     * @return string
     */
    private function numberFormat($value): string
    {
        return number_format($value);
    }

    /**
     * @param Player $player
     * @return string
     * @throws FilesystemException
     */
    public function getAvatar(Player $player): string
    {
        $path = 'user' . DIRECTORY_SEPARATOR . $player->getAvatar();
        if (!$this->appStorage->fileExists($path)) {
            $path = 'user' . DIRECTORY_SEPARATOR . 'default.png';
        }
        return $this->appStorage->read($path);
    }


    /**
     * @param Badge $badge
     * @return string
     * @throws FilesystemException
     */
    public function getBadge(Badge $badge): string
    {
        $path = 'badge' . DIRECTORY_SEPARATOR . $badge->getType() . DIRECTORY_SEPARATOR . $badge->getPicture();
        if (!$this->appStorage->fileExists($path)) {
            $path = 'badge' . DIRECTORY_SEPARATOR . 'default.gif';
        }
        return $this->appStorage->read($path);
    }
}
