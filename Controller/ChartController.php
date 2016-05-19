<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ChartController
 * @Route("/chart")
 */
class ChartController extends Controller
{


    /**
     * @Route("/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="chart_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function indexAction($id)
    {
        $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithGame($id);

        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserChart')->getRanking(
            array(
                'chart' => $chart,
                'idRecord' => $id,
                'maxRank' => 20,
            )
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($chart->getGroup()->getGame()->getLibJeu(), 'game_index', ['id' => $chart->getGroup()->getGame()->getIdJeu()]);
        $breadcrumbs->addRouteItem($chart->getGroup()->getLibGroupe(), 'group_index', ['id' => $chart->getGroup()->getIdGroupe()]);
        $breadcrumbs->addItem($chart->getLibRecord());

        return $this->render('VideoGamesRecordsCoreBundle:Chart:index.html.twig', array('chart' => $chart, 'ranking' => $ranking));
    }


}