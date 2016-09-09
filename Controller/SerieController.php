<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class SerieController
 * @Route("/serie")
 */
class SerieController extends Controller
{
    /**
     * @Route("/ranking-points", name="vgr_serie_ranking_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function rankingPointsAction()
    {
        $idSerie = $this->container->getParameter('videogamesrecords_core.idSerie');
        if ($idSerie === null) {
            throw $this->createNotFoundException('There is no serie on config website');
        }
        $rankingPoints = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserSerie')->getRankingPoints(
            array(
                'idSerie' => $idSerie,
                'idLogin' => null,
                'maxRank' => 100,
            )
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('serie.pointranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:user-points.html.twig', array('rankingPoints' => $rankingPoints));
    }


    /**
     * @Route("/ranking-medals", name="vgr_serie_ranking_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function rankingMedalsAction()
    {
        $idSerie = $this->container->getParameter('videogamesrecords_core.idSerie');
        if ($idSerie === null) {
            throw $this->createNotFoundException('There is no serie on config website');
        }
        $rankingMedals = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserSerie')->getRankingMedals(
            array(
                'idSerie' => $idSerie,
                'idLogin' => 1,
                'maxRank' => 100,
            )
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('serie.medalranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:user-medals.html.twig', array('rankingMedals' => $rankingMedals));
    }
}
