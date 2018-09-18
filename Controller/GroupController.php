<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Form\Type\SubmitFormFactory;

/**
 * Class GroupController
 */
class GroupController extends Controller
{


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function playerRankingPoints(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingPoints($group->getId(), $maxRank, $idPlayer);
        return $ranking;
    }


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function playerRankingMedals(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGroup')->getRankingMedals($group->getId(), $maxRank, $idPlayer);
        return $ranking;
    }


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function teamRankingPoints(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsTeamBundle:TeamGroup')->getRankingPoints($group->getId(), $maxRank, $idPlayer);
        return $ranking;
    }


    /**
     * @param Group    $group
     * @param Request $request
     * @return mixed
     */
    public function teamRankingMedals(Group $group, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsTeamBundle:TeamGroup')->getRankingMedals($group->getId(), $maxRank, $idPlayer);
        return $ranking;
    }



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

        $breadcrumbs = $this->getGroupBreadcrumbs($group);
        $breadcrumbs->addItem($group->getLibGroup());

        return $this->render('VideoGamesRecordsCoreBundle:Submit:form.html.twig', ['group' => $group, 'charts' => $charts, 'form' => $form->createView()]);
    }
}
