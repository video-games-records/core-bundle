<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class GameController
 * @Route("/player")
 */
class PlayerController extends Controller
{
    /**
     * @Route("/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_player_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction($id)
    {
        $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($id);

        $nbPlayer = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getNbPlayer(['nbChart>0' => true]);

        $rows = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->getRows(
            [
                'idPlayer' => $id,
                'limit' => 1,
                'orderBy' => [
                    'column' => 'pc.dateModif',
                    'order' => 'DESC',
                ],
            ]
        );
        if (count($rows) == 1) {
            $lastChart = $rows[0];
        } else {
            $lastChart = null;
        }

        //----- breadcrumbs
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addRouteItem('Home', 'homepage');
        $breadcrumbs->addItem($player->getPseudo());

        return $this->render('VideoGamesRecordsCoreBundle:Player:index.html.twig', ['player' => $player, 'nbPlayer' => $nbPlayer, 'lastChart' => $lastChart]);
    }
}
