<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\GameService;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerGameRanking;

/**
 * Class GameController
 * @Route("/game")
 */
class GameController extends DefaultController
{
    private PlayerGameRanking $playerGameRanking;
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


    /**
     * @param Request $request
     * @return mixed
     */
    public function listFromPlayerLostPosition(Request $request)
    {
        $locale = $request->getLocale();
        return $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')
            ->findFromlostPosition($this->getPlayer(), $locale);
    }


    /**
     * @Route("/day", name="game_of_day")
     * @return Response
     * @throws NonUniqueResultException
     */
    public function dayAction(): Response
    {
        $game = $this->gameService->getGameOfDay();
        return $this->render('VideoGamesRecordsCoreBundle:Default:game_day.html.twig', ['game' => $game]);
    }
}
