<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($page)
    {
        $query = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->queryAlpha();

        $paginator = $this->get('knp_paginator');
        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $games */
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
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);

        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingPoints(
            array(
                'idGame' => $id,
                'maxRank' => 5,
                'idPlayer' => null,
            )
        );

        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingMedals(
            array(
                'idGame' => $id,
                'maxRank' => 5,
                'idPlayer' => null,
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
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPointsAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);
        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingPoints(
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

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-points.html.twig', array('rankingPoints' => $rankingPoints));
    }


    /**
     * @Route("/ranking-medals/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_game_ranking_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingMedalsAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);
        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingMedals(
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

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-medals.html.twig', array('rankingMedals' => $rankingMedals));
    }
}
