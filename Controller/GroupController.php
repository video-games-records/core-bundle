<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Form\Type\SubmitFormFactory;

/**
 * Class GroupController
 * @Route("/group")
 */
class GroupController extends VgrBaseController
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

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibGame(), 'vgr_game_index', ['id' => $group->getGame()->getId()]);
        $breadcrumbs->addItem($group->getLibGroup());

        return $this->render(
            'VideoGamesRecordsCoreBundle:Group:index.html.twig',
            [
                'group' => $group,
                'playerRankingPoints' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingPoints($id, 5, null),
                'playerRankingMedals' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingMedals($id, 5, null),
                'teamRankingPoints' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGroup')->getRankingPoints($id, 5, null),
                'teamRankingMedals' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGroup')->getRankingMedals($id, 5, null),
            ]
        );
    }


    /**
     * @Route("/ranking-player-points/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_group_ranking_player_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPlayerPointsAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibGame(), 'vgr_game_index', ['id' => $group->getGame()->getId()]);
        $breadcrumbs->addRouteItem($group->getLibGroup(), 'vgr_group_index', ['id' => $id]);
        $breadcrumbs->addItem('game.pointchartranking.full');

        return $this->render(
            'VideoGamesRecordsCoreBundle:Ranking:player-points-chart.html.twig',
            [
                'ranking' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingPoints($id, 100, null),
            ]
        );
    }


    /**
     * @Route("/ranking-player-medals/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_group_ranking_player_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPlayerMedalsAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibGame(), 'vgr_game_index', ['id' => $group->getGame()->getId()]);
        $breadcrumbs->addRouteItem($group->getLibGroup(), 'vgr_group_index', ['id' => $id]);
        $breadcrumbs->addItem('game.medalranking.full');

        return $this->render(
            'VideoGamesRecordsCoreBundle:Ranking:player-medals.html.twig',
            [
                'ranking' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingMedals($id, 100, null),
            ]
        );
    }

    /**
     * @Route("/ranking-team-points/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_group_ranking_team_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingTeamPointsAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibGame(), 'vgr_game_index', ['id' => $group->getGame()->getId()]);
        $breadcrumbs->addRouteItem($group->getLibGroup(), 'vgr_group_index', ['id' => $id]);
        $breadcrumbs->addItem('game.pointchartranking.full');

        return $this->render(
            'VideoGamesRecordsCoreBundle:Ranking:team-points-chart.html.twig',
            [
                'ranking' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGroup')->getRankingPoints($id, 100, null),
            ]
        );
    }


    /**
     * @Route("/ranking-team-medals/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_group_ranking_team_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingTeamMedalsAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibGame(), 'vgr_game_index', ['id' => $group->getGame()->getId()]);
        $breadcrumbs->addRouteItem($group->getLibGroup(), 'vgr_group_index', ['id' => $id]);
        $breadcrumbs->addItem('game.medalranking.full');

        return $this->render(
            'VideoGamesRecordsCoreBundle:Ranking:team-medals.html.twig',
            [
                'ranking' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGroup')->getRankingMedals($id, 100, null),
            ]
        );
    }


    /**
     * @Route("/form/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_group_form")
     * @Method("GET")
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formAction($id)
    {
        $group = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Group')->getWithGame($id);
        $charts = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getFromGroupWithChartType($id);

        $data = [
            'id' => $id,
            'type' => 'group',
        ];

        $data = array_merge(
            $data,
            $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChartLib')->getFormValues($this->getPlayer(), null, $group)
        );

        $form = SubmitFormFactory::createSubmitForm(
            $this->get('form.factory')->create('Symfony\Component\Form\Extension\Core\Type\FormType', $data),
            $charts
        );


        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem($group->getGame()->getLibGame(), 'vgr_game_index', ['id' => $group->getGame()->getId()]);
        $breadcrumbs->addItem($group->getLibGroup());

        return $this->render('VideoGamesRecordsCoreBundle:Submit:form.html.twig', ['group' => $group, 'charts' => $charts, 'form' => $form->createView()]);
    }
}
