<?php

namespace VideoGamesRecords\CoreBundle\Controller\Api;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;

/**
 * Class GameController
 * @Route("/api/ranking")
 */
class RankingController extends Controller
{
    /** @var \Symfony\Component\Serializer\SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/ranking-player-points/{id}", requirements={"id": "[1-9]\d*"}, name="api_vgr_game_ranking_player_points")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankingPlayerPointsAction($id)
    {
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingPoints($id, 100, null);

        return new JsonResponse(
            $this->serializer->serialize($ranking, 'json', ['groups' => ['ranking_point']]),
            200,
            [],
            true
        );
    }


    /**
     * @Route("/ranking-player-medals/{id}", requirements={"id": "[1-9]\d*"}, name="api_vgr_game_ranking_player_medals")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function rankingPlayerMedalsAction($id)
    {
        $ranking = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingMedals($id, 100, null);

        return new JsonResponse(
            $this->serializer->serialize($ranking, 'json', ['groups' => ['ranking_medal']]),
            200,
            [],
            true
        );
    }
}
