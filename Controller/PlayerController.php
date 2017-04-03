<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PlayerController
 * @Route("/player")
 */
class PlayerController extends Controller
{
    /**
     * @Route("/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_player_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction($id)
    {
        $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($id);

        $nbPlayer = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getNbPlayer(['nbChart>0' => true]);

        $rows = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->getRows(
            [
                'idPlayer' => $id,
                'limit' => 1,
                'orderBy' => [
                    'column' => 'pc.dateModif',
                    'order' => 'DESC',
                ],
            ]
        );
        if (count($rows) == 1) {
            $lastChart = $rows[0];
        } else {
            $lastChart = null;
        }

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem($player->getPseudo());

        return $this->render('VideoGamesRecordsCoreBundle:Player:index.html.twig', ['player' => $player, 'nbPlayer' => $nbPlayer, 'lastChart' => $lastChart]);
    }

    /**
     * @Route("/ranking-points-chart", name="vgr_player_ranking_points_chart")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPointsChartAction()
    {
        $idPlayer = null;
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointsChart($idPlayer);
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('player.pointchartranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-points-chart.html.twig', ['ranking' => $ranking]);
    }

    /**
     * @Route("/ranking-points-game", name="vgr_player_ranking_points_game")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPointsGameAction()
    {
        $idPlayer = null;
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointsGame($idPlayer);
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('player.pointgameranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-points-game.html.twig', ['ranking' => $ranking]);
    }


    /**
     * @Route("/ranking-medals", name="vgr_player_ranking_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingMedalsAction()
    {
        $idPlayer = null;
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingMedals($idPlayer);
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('player.medalranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-medals.html.twig', ['ranking' => $ranking]);
    }

    /**
     * @Route("/ranking-cups", name="vgr_player_ranking_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingCupsAction()
    {
        $idPlayer = null;
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingCups($idPlayer);
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('player.cupranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-cups.html.twig', ['ranking' => $ranking]);
    }
}
