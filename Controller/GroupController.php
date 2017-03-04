<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class GroupController
 * @Route("/group")
 */
class GroupController extends Controller
{
    /**
     * @Route("/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_group_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);

        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingPoints(
            [
                'idGroup' => $id,
                'maxRank' => 5,
                'idPlayer' => null,
            ]
        );

        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingMedals(
            [
                'idGroup' => $id,
                'maxRank' => 5,
                'idPlayer' => null,
            ]
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibGame(), 'vgr_game_index', ['id' => $group->getGame()->getId()]);
        $breadcrumbs->addItem($group->getLibGroup());

        return $this->render('VideoGamesRecordsCoreBundle:Group:index.html.twig', ['group' => $group, 'rankingPoints' => $rankingPoints, 'rankingMedals' => $rankingMedals]);
    }


    /**
     * @Route("/ranking-points/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_group_ranking_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPointsAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);
        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingPoints(
            [
                'idGroup' => $id,
                'idLogin' => null,
            ]
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibGame(), 'vgr_game_index', ['id' => $group->getGame()->getId()]);
        $breadcrumbs->addRouteItem($group->getLibGroup(), 'vgr_group_index', ['id' => $id]);
        $breadcrumbs->addItem('game.pointranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-points.html.twig', ['rankingPoints' => $rankingPoints]);
    }


    /**
     * @Route("/ranking-medals/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_group_ranking_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingMedalsAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);
        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingMedals(
            [
                'idGroup' => $id,
                'idLogin' => null,
            ]
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibGame(), 'vgr_game_index', ['id' => $group->getGame()->getId()]);
        $breadcrumbs->addRouteItem($group->getLibGroup(), 'vgr_group_index', ['id' => $id]);
        $breadcrumbs->addItem('game.medalranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-medals.html.twig', ['rankingMedals' => $rankingMedals]);
    }
}
