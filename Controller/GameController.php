<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use VideoGamesRecords\CoreBundle\Entity\Game;

class GameController extends Controller
{

    /**
     * @Route("/game/list", defaults={"page": 1}, name="game_list")
     * @Route("/game/list/page/{page}", requirements={"page": "[1-9]\d*"}, name="game_list_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function listAction($page)
    {
        /*$games = $this->getDoctrine()
            ->getRepository('VideoGamesRecordsCoreBundle:Game')
            ->findBy(array(), array('libJeu_en' => 'ASC'));*/
        $idSerie = $this->container->getParameter('videogamesrecords_core.idSerie');

        $query = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->queryAlpha(
            array(
                'idSerie' => $idSerie,
            )
        );

        $paginator = $this->get('knp_paginator');
        $games = $paginator->paginate($query, $page, Game::NUM_ITEMS);
        $games->setUsedRoute('game_list_paginated');

        if (0 === count($games)) {
            throw $this->createNotFoundException();
        }

        return $this->render('VideoGamesRecordsCoreBundle:Game:list.html.twig', array('games' => $games));
    }


    /**
     * @Route("/game/index/id/{id}", requirements={"id": "[1-9]\d*"}, name="game_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function indexAction($id)
    {
        $idSerie = $this->container->getParameter('videogamesrecords_core.idSerie');
        $games = $this->container->getParameter('videogamesrecords_core.games');

        if ( ($idSerie != null) && (!in_array($id, $games)) ) {
            throw new \Exception('Invalid game for this serie');
        }

        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);

        return $this->render('VideoGamesRecordsCoreBundle:Game:index.html.twig', array('game' => $game));
    }
}
