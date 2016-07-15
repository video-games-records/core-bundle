<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use VideoGamesRecords\CoreBundle\Entity\Game;

/**
 * Class GameController
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * @Route("/list", defaults={"page": 1}, name="vgr_game_list")
     * @Route("/list/page/{page}", requirements={"page": "[1-9]\d*"}, name="vgr_game_list_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function listAction($page)
    {
        $idSerie = $this->container->getParameter('videogamesrecords_core.idSerie');

        $query = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->queryAlpha(
            array(
                'idSerie' => $idSerie,
            )
        );

        $paginator = $this->get('knp_paginator');
        $games = $paginator->paginate($query, $page, Game::NUM_ITEMS);
        $games->setUsedRoute('game_list_paginated');

        if (0 === count($games)) {
            throw $this->createNotFoundException();
        }

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('game.list');

        return $this->render('VideoGamesRecordsCoreBundle:Game:list.html.twig', array('games' => $games));
    }

    /**
     * @Route("/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_game_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function indexAction($id)
    {
        $idSerie = $this->container->getParameter('videogamesrecords_core.idSerie');
        $games = $this->container->getParameter('videogamesrecords_core.games');

        if ($idSerie !== null && !in_array($id, $games)) {
            throw new \Exception('Invalid game for this serie');
        }

        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);

        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGame')->getRankingPoints(
            array(
                'idGame' => $id,
                'maxRank' => 5,
                'idLogin' => null,
            )
        );

        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGame')->getRankingMedals(
            array(
                'idGame' => $id,
                'maxRank' => 5,
                'idLogin' => null,
            )
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem($game->getLibGame());

        return $this->render('VideoGamesRecordsCoreBundle:Game:index.html.twig', array('game' => $game, 'rankingPoints' => $rankingPoints, 'rankingMedals' => $rankingMedals));
    }

    /**
     * @Route("/ranking-points/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_game_ranking_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function rankingPointsAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);
        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGame')->getRankingPoints(
            array(
                'idGame' => $id,
                'idLogin' => null,
            )
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($game->getLibGame(), 'vgr_game_index', ['id' => $id]);
        $breadcrumbs->addItem('game.pointranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:user-points.html.twig', array('rankingPoints' => $rankingPoints));
    }


    /**
     * @Route("/ranking-medals/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_game_ranking_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function rankingMedalsAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);
        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGame')->getRankingMedals(
            array(
                'idGame' => $id,
                'idLogin' => null,
            )
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($game->getLibGame(), 'vgr_game_index', ['id' => $id]);
        $breadcrumbs->addItem('game.medalranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:user-medals.html.twig', array('rankingMedals' => $rankingMedals));
    }
}
