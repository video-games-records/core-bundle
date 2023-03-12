<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;

/**
 * Class GameController
 * @Route("/game")
 */
class GameController extends AbstractController
{
    private GameRepository $gameRepository;
    private FilesystemOperator $appStorage;

    private string $prefix = 'game/';

    public function __construct(GameRepository $gameRepository, FilesystemOperator $appStorage)
    {
        $this->gameRepository = $gameRepository;
        $this->appStorage = $appStorage;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function autocomplete(Request $request): mixed
    {
        $q = $request->query->get('query', null);
        $locale = $request->getLocale();
        return $this->gameRepository->autocomplete($q, $locale);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function listByLetter(Request $request): mixed
    {
        $letter = $request->query->get('letter', '0');
        $locale = $request->getLocale();
        return $this->gameRepository
            ->findWithLetter($letter, $locale)
            ->getResult();
    }

    /**
     * @Route(path="/{id}/picture", requirements={"id": "[1-9]\d*"}, name="vgr_core_game_picture", methods={"GET"})
     * @Cache(expires="+30 days")
     * @param Game $game
     * @return StreamedResponse
     * @throws FilesystemException
     */
    public function pictureAction(Game $game): StreamedResponse
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
