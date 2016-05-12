<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class GroupController extends Controller
{


    /**
     * @Route("/group/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="group_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function indexAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWidthGame($id);

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


        return $this->render('VideoGamesRecordsCoreBundle:Group:index.html.twig', array('group' => $group, 'rankingPoints' => $rankingPoints, 'rankingMedals' => $rankingMedals));
    }


    /**
     * @Route("/group/ranking-points/id/{id}", requirements={"id": "[1-9]\d*"}, name="group_ranking_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function rankingPointsAction($id)
    {
        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGroup')->getRankingPoints(
            array(
                'idGroupe' => $id,
                'idLogin' => null,
            )
        );

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:user-points.html.twig', array('rankingPoints' => $rankingPoints));
    }


    /**
     * @Route("/group/ranking-medals/id/{id}", requirements={"id": "[1-9]\d*"}, name="group_ranking_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function rankingMedalsAction($id)
    {
        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserGroup')->getRankingMedals(
            array(
                'idGroupe' => $id,
                'idLogin' => null,
            )
        );

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:user-medals.html.twig', array('rankingMedals' => $rankingMedals));
    }

}