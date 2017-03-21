<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use VideoGamesRecords\CoreBundle\Form\Type\SubmitFormFactory;

/**
 * Class ChartController
 * @Route("/chart")
 */
class ChartController extends VgrBaseController
{
    /**
     * @Route("/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_chart_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($id)
    {
        $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithGame($id);

        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->getRanking(
            [
                'chart' => $chart,
                'idChart' => $id,
                'maxRank' => 20,
            ]
        );

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($chart->getGroup()->getGame()->getLibGame(), 'vgr_game_index', ['id' => $chart->getGroup()->getGame()->getId()]);
        $breadcrumbs->addRouteItem($chart->getGroup()->getLibGroup(), 'vgr_group_index', ['id' => $chart->getGroup()->getId()]);
        $breadcrumbs->addItem($chart->getLibChart());

        return $this->render('VideoGamesRecordsCoreBundle:Chart:index.html.twig', ['chart' => $chart, 'ranking' => $ranking]);
    }

    /**
     * @Route("/form/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_chart_form")
     * @Method("GET")
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formAction($id)
    {
        $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($id);
        $charts = [$chart];

        $data = [
            'id' => $id,
            'type' => 'chart',
        ];

        $data = array_merge(
            $data,
            $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChartLib')->getFormValues($this->getPlayer(), $chart)
        );

        $form = SubmitFormFactory::createSubmitForm(
            $this->get('form.factory')->create('Symfony\Component\Form\Extension\Core\Type\FormType', $data),
            $charts
        );


        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($chart->getGroup()->getGame()->getLibGame(), 'vgr_game_index', ['id' => $chart->getGroup()->getGame()->getId()]);
        $breadcrumbs->addRouteItem($chart->getGroup()->getLibGroup(), 'vgr_group_index', ['id' => $chart->getGroup()->getId()]);
        $breadcrumbs->addItem($chart->getLibChart());

        return $this->render('VideoGamesRecordsCoreBundle:Submit:form.html.twig', ['chart' => $chart, 'charts' => $charts, 'form' => $form->createView()]);
    }
}
