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

/**
 * Class GameController
 * @Route("/api/game")
 */
class GameController extends Controller
{
    /** @var \Symfony\Component\Serializer\SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/list", defaults={"letter": 0}, name="api_vgr_game_list")
     * @Route("/list/letter/{letter}", requirements={"letter": "[0|A-Z]"}, name="api_vgr_game_list_letter")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $letter
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction(Request $request, $letter)
    {
        $page = (int)$request->get('offset', 1);
        $limit = (int)$request->get('limit', 10);

        $gameQuery = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')
            ->findWithLetter($letter, $request->getLocale());

        $gameQuery
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);
        $gamePaginator = new Paginator($gameQuery, true);

        return new JsonResponse(
            $this->serializer->serialize($gamePaginator->getIterator()->getArrayCopy(), 'json', ['groups' => ['game_list']]),
            200,
            [],
            true
        );
    }

    /**
     * @Route("/{id}", requirements={"id": "[1-9]\d*"}, name="api_vgr_game_index")
     * @Method("GET")
     * @Cache(smaxage="10")
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction($id)
    {
        $game = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->find($id);

        return new JsonResponse(
            $this->serializer->serialize($game, 'json', ['groups' => ['game_show']]),
            200,
            [],
            true
        );
    }
}
