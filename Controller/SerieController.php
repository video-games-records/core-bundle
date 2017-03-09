<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class SerieController
 * @Route("/serie")
 */
class SerieController extends VgrBaseController
{
    public function listAction()
    {
        //@todo define how we list them (same way as game?)
    }

    public function indexAction($id)
    {
        //@todo display a series with its game list and ranking
    }

    /**
     * @Route("/ranking-points/{id}", name="vgr_serie_ranking_points", requirements={"id": "[1-9]\d*"})
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPointsAction($id)
    {
        $rankingPoints = $this
            ->getDoctrine()
            ->getRepository('VideoGamesRecordsCoreBundle:PlayerSerie')
            ->getRankingPoints($id, $this->getPlayer(), 100);

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('serie.pointranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-points.html.twig', ['rankingPoints' => $rankingPoints]);
    }

    /**
     * @Route("/ranking-medals/{id}", name="vgr_serie_ranking_medals", requirements={"id": "[1-9]\d*"})
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingMedalsAction($id)
    {
        $rankingMedals = $this
            ->getDoctrine()
            ->getRepository('VideoGamesRecordsCoreBundle:PlayerSerie')
            ->getRankingMedals($id, $this->getPlayer(), 100);

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('serie.medalranking.full');

        return $this->render('VideoGamesRecordsCoreBundle:Ranking:player-medals.html.twig', ['rankingMedals' => $rankingMedals]);
    }
}
