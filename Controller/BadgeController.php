<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class BadgeController
 * @Route("/list")
 */
class BadgeController extends VgrBaseController
{

    /**
     * @Route("/list", requirements={"id": "[1-9]\d*"}, name="vgr_badge_player_list")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $idPlayer
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function playerListAction($idPlayer, $type)
    {
        return $this->render(
            'VideoGamesRecordsCoreBundle:Badge:list.html.twig',
            [
                'title' => ucfirst($type),
                'list' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerBadge')->getFromPlayer($idPlayer, $type),
            ]
        );
    }

    /**
     * @Route("/list", requirements={"id": "[1-9]\d*"}, name="vgr_badge_team_list")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $idTeam
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function teamListAction($idTeam, $type)
    {
        return $this->render(
            'VideoGamesRecordsCoreBundle:Badge:list.html.twig',
            [
                'title' => ucfirst($type),
                'list' => $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamBadge')->getFromTeam($idTeam, $type),
            ]
        );
    }
}
