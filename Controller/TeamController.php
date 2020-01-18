<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Entity\Team;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Form\Type\TeamForm;
use VideoGamesRecords\CoreBundle\Form\Type\ChangeLeaderForm;

/**
 * Class TeamController
 * @Route("/team")
 */
class TeamController extends Controller
{
    /**
     * @return Team|null
     */
    private function getTeam()
    {
        if ($this->getUser() !== null) {
            $player =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($this->getUser());
            return $player->getTeam();
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function rankingPointChart()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingPointChart($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingPointGame()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingPointGame($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingMedal()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingMedal($this->getTeam());
    }

    /**
     * @return mixed
     */
    public function rankingCup()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getRankingCup($this->getTeam());
    }






    /***************** OLD CODE *********************/




    /**
     * @Route("/{id}/{slug}", requirements={"id": "[1-9]\d*"}, name="vgr_team_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction($id, $slug)
    {
        /** @var \VideoGamesRecords\CoreBundle\Entity\Team $team */
        $team = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->getTeamWithGames($id);
        if ($slug !== $team->getSlug()) {
            return $this->redirectToRoute('vgr_team_index', ['id' => $team->getIdTeam(), 'slug' => $team->getSlug()], 301);
        }

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

    /**
     * @Route("/account", name="vgr_team_account")
     * @Method("GET")
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function accountAction()
    {
        /** @var \VideoGamesRecords\CoreBundle\Entity\Player $player */
        $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($this->getPlayer()->getIdPlayer());

        return $this->render(
            'VideoGamesRecordsCoreBundle:Team:account.html.twig',
            [
                'player' => $player,
            ]
        );
    }

    /**
     * @Route("/quit", name="vgr_team_quit")
     * @Method({"GET","POST"})
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function quitAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('vgr_team_quit'))
            ->setMethod('POST')
            ->add('save', SubmitType::class, array('label' => 'QUIT'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var \VideoGamesRecords\CoreBundle\Entity\Player $player */
            $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($this->getPlayer()->getIdPlayer());
            $player->setTeam(null);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            //----- Message
            $this->addFlash(
                'notice',
                sprintf('Your changes were saved!!!')
            );

            return $this->redirectToRoute('vgr_account_index');
        }

        return $this->render(
            'VideoGamesRecordsCoreBundle:Form:form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/create", name="vgr_team_create")
     * @Method({"GET","POST"})
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem('Account', 'vgr_account_index');
        $breadcrumbs->addItem('Create team');

        /** @var \VideoGamesRecords\CoreBundle\Entity\Player $player */
        $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($this->getPlayer()->getIdPlayer());

        $team = new Team();

        $form = $this->createForm(TeamForm::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $team->setIdLeader($player->getIdPlayer());
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();

            $player->setTeam($team);
            $em->flush();

            //----- Message
            $this->addFlash(
                'notice',
                sprintf('Your changes were saved!!!')
            );

            return $this->redirectToRoute('vgr_account_index');
        }

        return $this->render(
            'VideoGamesRecordsCoremBundle:Team:create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/update", name="vgr_team_update")
     * @Method({"GET","POST"})
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem('Account', 'vgr_account_index');
        $breadcrumbs->addItem('Update team');

        /** @var \VideoGamesRecords\CoreBundle\Entity\Player $player */
        $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($this->getPlayer()->getIdPlayer());

        $team = $player->getTeam();

        $form = $this->createForm(TeamForm::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            //----- Message
            $this->addFlash(
                'notice',
                sprintf('Your changes were saved!!!')
            );

            return $this->redirectToRoute('vgr_account_index');
        }

        return $this->render(
            'VideoGamesRecordsCoreBundle:Team:update.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/change-leader", name="vgr_team_change_leader")
     * @Method({"GET","POST"})
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function changeLeaderAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addRouteItem('Account', 'vgr_account_index');
        $breadcrumbs->addItem('Change leader');

        /** @var \VideoGamesRecords\CoreBundle\Entity\Player $player */
        $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($this->getPlayer()->getIdPlayer());

        $players = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getPlayersFromTeam($player->getTeam()->getIdTeam());
        $choices = array();
        /** @var \VideoGamesRecords\CoreBundle\Entity\Player $row */
        foreach ($players as $row) {
            if ($this->getPlayer()->getIdPlayer() != $row->getIdPlayer()) {
                $choices[$row->getPseudo()] = $row->getIdPlayer();
            }
        }
        $form = $this->createForm(ChangeLeaderForm::class, null, array('players' => $choices));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $team = $player->getTeam();
            $team->setIdLeader($data['idPlayer']);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            //----- Message
            $this->addFlash(
                'notice',
                sprintf('Your changes were saved!!!')
            );

            return $this->redirectToRoute('vgr_account_index');
        }

        return $this->render(
            'VideoGamesRecordsCoreBundle:Team:change-leader.html.twig',
            [
                'form' => $form->createView(),
                'nbChoices' => count($choices)
            ]
        );
    }
}
