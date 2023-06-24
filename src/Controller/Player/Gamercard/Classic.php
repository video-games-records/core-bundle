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
use VideoGamesRecords\CoreBundle\File\Picture;
use VideoGamesRecords\CoreBundle\File\PictureCreatorFactory;
use VideoGamesRecords\CoreBundle\Repository\PlayerGameRepository;
use VideoGamesRecords\CoreBundle\Traits\GetOrdinalSuffixTrait;
use VideoGamesRecords\CoreBundle\Traits\NumberFormatTrait;

/**
 * @Route("/gamercard")
 * @Cache(expires="tomorrow", public=true)
 */
class Classic extends AbstractController
{
    use GetOrdinalSuffixTrait;
    use NumberFormatTrait;

    private FilesystemOperator $appStorage;
    private PlayerGameRepository $playerGameRepository;

    public function __construct(
        FilesystemOperator $appStorage,
        PlayerGameRepository $playerGameRepository
    ) {
        $this->appStorage = $appStorage;
        $this->playerGameRepository = $playerGameRepository;
    }

    /**
     * @Route("/{id}/classic", name="gamercard_classic_1", methods={"GET"})
     * @Route("/classic/{id}", name="gamercard_classic_2", methods={"GET"})
     * @Cache(smaxage="900")
     * @param Player $player
     * @throws FilesystemException
     * @throws Exception
     */
    public function __invoke(Player $player): void
    {
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
            ->addFont('segoeUILight', '../../../Resources/fonts/segoeuil.ttf')
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
            ->addFont('segoeUISemiBold', '../../../Resources/fonts/seguisb.ttf')
            ->write($player->getChartRank0(), $fontSize, 96, 70)
            ->write($player->getChartRank1(), $fontSize, 145, 70)
            ->write($player->getChartRank2(), $fontSize, 96, 90)
            ->write($player->getChartRank3(), $fontSize, 145, 90)
            ->write($rankMedal, $fontSize, 175, 80)
            ->write($pointGame, $fontSize, 82, 45);

        // Add sprites pictures medals
        $sprite = PictureCreatorFactory::fromFile('../../../Resources/img/sprite.png');
        $gamercard
            ->copyResized($sprite, 78, 59, 126, 160, 16, 16, 16, 16)
            ->copyResized($sprite, 127, 59, 108, 160, 16, 16, 16, 16)
            ->copyResized($sprite, 78, 79, 92, 160, 16, 16, 16, 16)
            ->copyResized($sprite, 127, 79, 74, 160, 16, 16, 16, 16);

        // Add avatar
        $avatar = PictureCreatorFactory::fromStream($this->getAvatar($player));
        $gamercard->copyResized($avatar, 9, 30, 0, 0, 64, 64);

        $playerGames = $this->playerGameRepository->findBy(['player' => $player], ['lastUpdate' => 'DESC'], 5);

        $startX = 9;
        foreach ($playerGames as $playerGame) {
            $picture = PictureCreatorFactory::fromStream($this->getBadge($playerGame->getGame()->getBadge()));
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
