<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Entity\Team;

/**
 * Class TeamController
 * @Route("/team")
 */
class TeamController extends Controller
{
    /**
     * @Route("/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_team_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction($id)
    {
        $team = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->find($id);

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem($team->getLibTeam());

        return $this->render('VideoGamesRecordsCoreBundle:Team:index.html.twig', ['team' => $team]);
    }


    /**
     * @Route("/list", defaults={"page": 1}, name="vgr_team_list")
     * @Route("/list/page/{page}", requirements={"page": "[1-9]\d*"}, name="vgr_team_list_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($page)
    {
        $query = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getPaginatedQuery();

        $paginator = $this->get('knp_paginator');
        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $teams */
        $teams = $paginator->paginate($query, $page, Team::NUM_ITEMS);
        $teams->setUsedRoute('vgr_team_list_paginated');

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem('team.list');

        return $this->render('VideoGamesRecordsCoreBundle:Team:list.html.twig', ['teams' => $teams]);
    }
}