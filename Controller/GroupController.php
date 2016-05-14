<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class GroupController
 * @Route("/group")
 */
class GroupController extends Controller
{


    /**
     * @Route("/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="group_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function indexAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);

        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGroup')->getRankingPoints(
            array(
                'idGroupe' => $id,
                'maxRank' => 5,
                'idLogin' => null,
            )
        );

        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGroup')->getRankingMedals(
            array(
                'idGroupe' => $id,
                'maxRank' => 5,
                'idLogin' => null,
            )
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibJeu(), 'game_index', ['id' => $id]);
        $breadcrumbs->addItem($group->getLibGroupe());

        return $this->render('VideoGamesRecordsCoreBundle:Group:index.html.twig', array('group' => $group, 'rankingPoints' => $rankingPoints, 'rankingMedals' => $rankingMedals));
    }


    /**
     * @Route("/ranking-points/id/{id}", requirements={"id": "[1-9]\d*"}, name="group_ranking_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function rankingPointsAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);
        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGroup')->getRankingPoints(
            array(
                'idGroupe' => $id,
                'idLogin' => null,
            )
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibJeu(), 'game_index', ['id' => $id]);
        $breadcrumbs->addRouteItem($group->getLibGroupe(), 'group_index', ['id' => $id]);
        $breadcrumbs->addItem('game.pointranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:user-points.html.twig', array('rankingPoints' => $rankingPoints));
    }


    /**
     * @Route("/ranking-medals/id/{id}", requirements={"id": "[1-9]\d*"}, name="group_ranking_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function rankingMedalsAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);
        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGroup')->getRankingMedals(
            array(
                'idGroupe' => $id,
                'idLogin' => null,
            )
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibJeu(), 'game_index', ['id' => $id]);
        $breadcrumbs->addRouteItem($group->getLibGroupe(), 'group_index', ['id' => $id]);
        $breadcrumbs->addItem('game.medalranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:user-medals.html.twig', array('rankingMedals' => $rankingMedals));
    }
}
