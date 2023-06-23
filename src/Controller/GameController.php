<?php

namespace VideoGamesRecords\CoreBundle\Controller;


use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;

/**
 * Class GameController
 * @Route("/game")
 */
class GameController extends AbstractController
{
    private GameRepository $gameRepository;


    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
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

}
