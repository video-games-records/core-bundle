<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\Game;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DBALException;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PlayerChartController
 * @Route("/player-chart")
 */
class PlayerChartController extends Controller
{

    private $em;
    private $userManager;

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $em)
    {
        $this->userManager = $userManager;
        $this->em = $em;
    }

    /**
     *
     */
    public function getPlayer()
    {
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
            ->getPlayerFromUser($this->getUser());
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function majPlatform(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $idGame = $data['idGame'];
        $idPlatform = $data['idPlatform'];

        $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->majPlatform(
            $this->getPlayer(),
            $this->em->getReference(Game::class, $idGame),
            $this->em->getReference(Platform::class, $idPlatform)
        );
        return new JsonResponse(['data' => true]);
    }

    /**
     * @Route("/top-score", name="playerChart_top_score")
     * @Method("GET")
     * @Cache(smaxage="10")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rssAction(Request $request)
    {
        $idGame = $request->query->get('idGame', null);
        $idGroup = $request->query->get('idGroup', null);

        $playerCharts = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->rssTopScore($idGame, $idGroup);

        $feed = $this->get('eko_feed.feed.manager')->get('player.chart.high.scores');

        // Add prefixe link
        foreach ($playerCharts as $playerChart) {
            $playerChart->setLink($feed->get('link') . $playerChart->getChart()->getId() . '/' . $playerChart->getChart()->getSlug());
        }

        $feed->addFromArray($playerCharts);
        return new Response($feed->render('rss'));
    }
}
