<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Class PlayerController
 * @Route("/player")
 */
class PlayerController extends Controller
{

    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return Player|null
     */
    private function getPlayer()
    {
        if ($this->getUser() !== null) {
            return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($this->getUser());
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function rankingPointChart()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointChart($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingPointGame()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingPointGame($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingMedal()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingMedal($this->getPlayer());
    }

    /**
     * @return mixed
     */
    public function rankingCup()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingCup($this->getPlayer());
    }





    /************* OLD CODE ******************/

    /**
     * @Route("/{id}/{slug}", requirements={"id": "[1-9]\d*"}, name="vgr_player_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($id, $slug)
    {
        $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getPlayerWithGames($id);
        if ($slug !== $player->getSlug()) {
            return $this->redirectToRoute('vgr_player_index', ['id' => $player->getIdPlayer(), 'slug' => $player->getSlug()], 301);
        }

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

        return $this->render(
            'VideoGamesRecordsCoreBundle:Player:index.html.twig',
            [
                'player' => $player,
                'nbPlayer' => $nbPlayer,
                'lastChart' => $lastChart
            ]
        );
    }
}
