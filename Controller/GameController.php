<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Service\GameService;

/**
 * Class GameController
 * @Route("/game")
 */
class GameController extends AbstractController
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function autocomplete(Request $request)
    {
        $q = $request->query->get('query', null);
        $locale = $request->getLocale();
        return $this->gameService->autocomplete($q, $locale);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function listByLetter(Request $request)
    {
        $letter = $request->query->get('letter', '0');
        $locale = $request->getLocale();
        return $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')
            ->findWithLetter($letter, $locale)
            ->getResult();
    }
}
