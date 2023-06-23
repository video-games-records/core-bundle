<?php

namespace VideoGamesRecords\CoreBundle\Controller\Game;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Game;

/**
 * @Route("/game")
 */
class GetPicture extends AbstractController
{
    private FilesystemOperator $appStorage;

    private string $prefix = 'game/';

    public function __construct(FilesystemOperator $appStorage)
    {
        $this->appStorage = $appStorage;
    }

    /**
     * @Route(path="/{id}/picture", requirements={"id": "[1-9]\d*"}, name="vgr_core_game_picture", methods={"GET"})
     * @Cache(expires="+30 days")
     * @param Game $game
     * @return StreamedResponse
     * @throws FilesystemException
     */
    public function __invoke(Game $game): StreamedResponse
    {
        $path = $this->prefix . $game->getPicture();
        if (!$this->appStorage->fileExists($path)) {
            $path = $this->prefix . 'default.png';
        }

        $stream = $this->appStorage->readStream($path);
        return new StreamedResponse(function() use ($stream) {
            fpassthru($stream);
        }, 200, ['Content-Type' => 'image/png']);
    }
}
