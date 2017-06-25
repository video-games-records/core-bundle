<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;

/**
 * Class GameController
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * @Route("/list", defaults={"letter": 0}, name="vgr_game_list")
     * @Route("/list/letter/{letter}", requirements={"letter": "[0|A-Z]"}, name="vgr_game_list_letter")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $letter
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, $letter)
    {
        $games = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')
            ->findWithLetter($letter, $request->getLocale());

        $alphabet = array_merge(['0'], range('A', 'Z'));


        /*$paginator = $this->get('knp_paginator');
        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $games */
        /*$games = $paginator->paginate($query, $page, Game::NUM_ITEMS);
        $games->setUsedRoute('vgr_game_list_paginated');*/

        /*if (0 === count($games)) {
            throw $this->createNotFoundException();
        }*/

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('game.list');

        return $this->render('VideoGamesRecordsCoreBundle:Game:list.html.twig', ['games' => $games, 'alphabet' => $alphabet]);
    }

    /**
     * @Route("/{id}/{slug}", requirements={"id": "[1-9]\d*"}, name="vgr_game_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction($id, $slug)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);
        if ($slug !== $game->getSlug()) {
            return $this->redirectToRoute('vgr_game_index', ['id' => $game->getId(), 'slug' => $game->getSlug()], 301);
        }

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem($game->getLibGame());

        return $this->render(
            'VideoGamesRecordsCoreBundle:Game:index.html.twig',
            [
                'game' => $game,
                'playerRankingPoints' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingPoints($id, 5, null),
                'playerRankingMedals' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingMedals($id, 5, null),
                'teamRankingPoints' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGame')->getRankingPoints($id, 5, null),
                'teamRankingMedals' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGame')->getRankingMedals($id, 5, null),
            ]
        );
    }

    /**
     * @Route("/ranking-player-points/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_game_ranking_player_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPlayerPointsAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);

        $breadcrumbs = $this->getGameBreadcrumbs($game);
        $breadcrumbs->addItem('game.pointchartranking.full');

        return $this->render(
            'VideoGamesRecordsCoreBundle:Ranking:player-points-chart.html.twig',
            [
                'ranking' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingPoints($id, 100, null),
            ]
        );
    }


    /**
     * @Route("/ranking-player-medals/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_game_ranking_player_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPlayerMedalsAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);

        $breadcrumbs = $this->getGameBreadcrumbs($game);
        $breadcrumbs->addItem('game.medalranking.full');

        return $this->render(
            'VideoGamesRecordsCoreBundle:Ranking:player-medals.html.twig',
            [
                'ranking' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingMedals($id, 100, null),
            ]
        );
    }


    /**
     * @Route("/ranking-team-points/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_game_ranking_team_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingTeamPointsAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);

        $breadcrumbs = $this->getGameBreadcrumbs($game);
        $breadcrumbs->addItem('game.pointchartranking.full');

        return $this->render(
            'VideoGamesRecordsCoreBundle:Ranking:team-points-chart.html.twig',
            [
                'ranking' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGame')->getRankingPoints($id, 100, null),
            ]
        );
    }


    /**
     * @Route("/ranking-team-medals/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_game_ranking_team_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingTeamMedalsAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);

        $breadcrumbs = $this->getGameBreadcrumbs($game);
        $breadcrumbs->addItem('game.medalranking.full');

        return $this->render(
            'VideoGamesRecordsCoreBundle:Ranking:team-medals.html.twig',
            [
                'ranking' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGame')->getRankingMedals($id, 100, null),
            ]
        );
    }

    /**
     * @param \VideoGamesRecords\CoreBundle\Entity\Game $game
     * @return object|\WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    private function getGameBreadcrumbs(Game $game)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($game->getLibGame(), 'vgr_game_index', ['id' => $game->getId(), 'slug' => $game->getSlug()]);

        return $breadcrumbs;
    }
}
