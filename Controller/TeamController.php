<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use VideoGamesRecords\CoreBundle\Entity\Team;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\TeamDemand;
use VideoGamesRecords\CoreBundle\Form\Team\DemandForm;
use VideoGamesRecords\CoreBundle\Form\Team\ChangeLeaderForm;

/**
 * Class TeamController
 * @Route("/team")
 */
class TeamController extends VgrBaseController
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
        /** @var \VideoGamesRecords\CoreBundle\Entity\Team $team */
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
            'VideoGamesRecordsCoreBundle:Team:quit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/cancel", name="vgr_team_cancel")
     * @Method({"GET","POST"})
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param Request $request
     * @param integer $idDemand
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function cancelAction(Request $request, $idDemand = null)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('vgr_team_cancel'))
            ->setMethod('POST')
            ->add('idDemand', HiddenType::class, array('data' => $idDemand))
            ->add('save', SubmitType::class, array('label' => 'CANCEL'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            /** @var \VideoGamesRecords\CoreBundle\Entity\TeamDemand $demand */
            $demand = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamDemand')->find($data['idDemand']);
            if ($demand->getStatus() == TeamDemand::STATUS_ACTIVE) {
                $demand->setStatus(TeamDemand::STATUS_CANCELED);
                $em->flush();
            }

            //----- Message
            $this->addFlash(
                'notice',
                sprintf('Your changes were saved!!!')
            );

            return $this->redirectToRoute('vgr_account_index');
        }

        return $this->render(
            'VideoGamesRecordsCoreBundle:Team:quit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/accept", name="vgr_team_accept")
     * @Method({"GET","POST"})
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param Request $request
     * @param integer $idDemand
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function acceptAction(Request $request, $idDemand = null)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('vgr_team_accept'))
            ->setMethod('POST')
            ->add('idDemand', HiddenType::class, array('data' => $idDemand))
            ->add('save', SubmitType::class, array('label' => 'ACCEPT'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            /** @var \VideoGamesRecords\CoreBundle\Entity\TeamDemand $demand */
            $demand = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamDemand')->find($data['idDemand']);

            if ($demand->getStatus() == TeamDemand::STATUS_ACTIVE) {
                $demand->setStatus(TeamDemand::STATUS_ACCEPTED);
                /** @var \VideoGamesRecords\CoreBundle\Entity\Player $player */
                $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($demand->getIdPlayer());
                $player->setTeam($demand->getTeam());

                $message = sprintf('Your changes were saved!!!');

                //----- Cancel all active demands for this player
                $demands = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamDemand')->getFromPlayer($player->getIdPlayer());
                foreach ($demands as $demand) {
                    $demand->setStatus(TeamDemand::STATUS_CANCELED);
                }
                $em->flush();
            } else {
                $message = sprintf('The player has already joined a team');
            }

            //----- Message
            $this->addFlash('notice', $message);

            return $this->redirectToRoute('vgr_account_index');
        }

        return $this->render(
            'VideoGamesRecordsCoreBundle:Team:accept.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/refuse", name="vgr_team_refuse")
     * @Method({"GET","POST"})
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param Request $request
     * @param integer $idDemand
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function refuseAction(Request $request, $idDemand = null)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('vgr_team_refuse'))
            ->setMethod('POST')
            ->add('idDemand', HiddenType::class, array('data' => $idDemand))
            ->add('save', SubmitType::class, array('label' => 'REFUSE'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            /** @var \VideoGamesRecords\CoreBundle\Entity\TeamDemand $demand */
            $demand = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamDemand')->find($data['idDemand']);
            $demand->setStatus(TeamDemand::STATUS_REFUSED);
            $em->flush();

            //----- Message
            $this->addFlash(
                'notice',
                sprintf('Your changes were saved!!!')
            );

            return $this->redirectToRoute('vgr_account_index');
        }

        return $this->render(
            'VideoGamesRecordsCoreBundle:Team:refuse.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/join", name="vgr_team_join")
     * @Method({"GET","POST"})
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @param Request $request
     * @param integer $idTeam
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function joinAction(Request $request, $idTeam = null)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('vgr_team_join'))
            ->setMethod('POST')
            ->add('idTeam', HiddenType::class, array('data' => $idTeam))
            ->add('save', SubmitType::class, array('label' => 'JOIN'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var \VideoGamesRecords\CoreBundle\Entity\Team $team */
            $team = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Team')->find($data['idTeam']);

            /** @var \VideoGamesRecords\CoreBundle\Entity\Player $player */
            $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($this->getPlayer()->getIdPlayer());
            if ($player->getTeam() != null) {
                //----- Message
                $this->addFlash(
                    'notice',
                    sprintf('Your are already join the team %s, you may re login on the site to see changes', $player->getTeam()->getLibTeam())
                );
                return $this->redirectToRoute('vgr_account_index');
            }

            $demand = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamDemand')->getFromPlayerAndTeam($this->getPlayer()->getIdPlayer(), $data['idTeam']);

            if ($demand !== null) {
                //----- Message
                $this->addFlash(
                    'notice',
                    sprintf('Your have already ask to join the team %s', $team->getLibTeam())
                );
            } else {
                $em = $this->getDoctrine()->getManager();
                //----- Create demand
                $demand = new TeamDemand();
                $demand->setPlayer($em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $this->getPlayer()->getIdPlayer()));
                $demand->setTeam($team);
                $em->persist($demand);
                $em->flush();

                //----- Message
                $this->addFlash(
                    'notice',
                    sprintf('Your demand to join team %s is sended to leader of the team', $team->getLibTeam())
                );
            }

            return $this->redirectToRoute('vgr_account_index');
        }

        return $this->render(
            'VideoGamesRecordsCoreBundle:Team:join.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/join", name="vgr_team_demands")
     * @Method({"GET","POST"})
     * @Cache(smaxage="10")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function demandsAction()
    {
        /** @var \VideoGamesRecords\CoreBundle\Entity\Player $player */
        $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($this->getPlayer()->getIdPlayer());

        if ($player->isLeader()) {
            $demands = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamDemand')->getFromTeam($player->getTeam()->getIdTeam());
        } else if ($player->getTeam() == null) {
            $demands = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamDemand')->getFromPlayer($player->getIdPlayer());
        } else {
            return;
        }

        return $this->render(
            'VideoGamesRecordsCoreBundle:Team:demands.html.twig',
            [
                'demands' => $demands,
                'isLeader' => $player->isLeader(),
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

        $form = $this->createForm(DemandForm::class, $team);

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
            'VideoGamesRecordsCoreBundle:Team:create.html.twig',
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

        $form = $this->createForm(DemandForm::class, $team);

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

        $players = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getPlayersFromTeam($player->getIdTeam());
        $choices = array();
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
            ]
        );
    }
}
