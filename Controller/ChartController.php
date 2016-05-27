<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use VideoGamesRecords\CoreBundle\Form\Type\SubmitForm;

/**
 * Class ChartController
 * @Route("/chart")
 */
class ChartController extends Controller
{


    /**
     * @Route("/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_chart_index")
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
        $breadcrumbs->addRouteItem($chart->getGroup()->getGame()->getLibJeu(), 'vgr_game_index', ['id' => $chart->getGroup()->getGame()->getIdJeu()]);
        $breadcrumbs->addRouteItem($chart->getGroup()->getLibGroupe(), 'vgr_group_index', ['id' => $chart->getGroup()->getIdGroupe()]);
        $breadcrumbs->addItem($chart->getLibRecord());

        return $this->render('VideoGamesRecordsCoreBundle:Chart:index.html.twig', array('chart' => $chart, 'ranking' => $ranking));
    }


    /**
     * @Route("/form/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_chart_form")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function formAction($id)
    {

        $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($id);
        $charts = array($chart);

        $form = \VideoGamesRecords\CoreBundle\Form\Type\SubmitFormFactory::createSubmitForm(
            $this->get('form.factory')->create('Symfony\Component\Form\Extension\Core\Type\FormType', array('id' => $id, 'type' => 'chart')),
            $charts
        );


        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($chart->getGroup()->getGame()->getLibJeu(), 'vgr_game_index', ['id' => $chart->getGroup()->getGame()->getIdJeu()]);
        $breadcrumbs->addRouteItem($chart->getGroup()->getLibGroupe(), 'vgr_group_index', ['id' => $chart->getGroup()->getIdGroupe()]);
        $breadcrumbs->addItem($chart->getLibRecord());

        return $this->render('VideoGamesRecordsCoreBundle:Submit:form.html.twig', array('chart' => $chart, 'charts' => $charts, 'form' => $form->createView()));
    }


}