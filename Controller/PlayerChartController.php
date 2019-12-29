<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DBALException;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Picture;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use VideoGamesRecords\CoreBundle\Exception\AccessDeniedException;

/**
 * Class PlayerChartController
 * @Route("/player-chart")
 */
class PlayerChartController extends Controller
{

    private $em;
    private $userManager;

    private $extensions = array(
        'text/plain' => '.txt',
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
    );

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


    /**
     * @param PlayerChart $playerChart
     * @param Request     $request
     * @return Response
     * @throws \Exception
     */
    public function sendPicture(PlayerChart $playerChart, Request $request)
    {
        if ($playerChart->getPlayer() != $this->getPlayer()) {
            throw new AccessDeniedException('ACESS DENIED');
        }
        if (!in_array($playerChart->getStatus()->getIdStatus(), PlayerChartStatus::getStatusForProving())) {
            throw new AccessDeniedException('ACESS DENIED');
        }

        $id = $playerChart->getId();
        $data = json_decode($request->getContent(), true);
        $file = $data['file'];
        $fp = fopen($file, 'r');
        $meta = stream_get_meta_data($fp);

        $idPlayer = $playerChart->getPlayer()->getId();
        $idGame = $playerChart->getChart()->getGroup()->getGame()->getId();
        $metadata = [
            'idplayer' => $idPlayer,
            'idgame' => $idGame,
        ];
        $key = $idPlayer . '/' . $idGame . '/'. uniqid() . $this->extensions[$meta['mediatype']];

        $s3 = $this->get('aws.s3');
        $s3->putObject([
            'Bucket' => $_ENV['AWS_BUCKET_PROOF'],
            'Key'    => $key,
            'Body'   => $fp,
            'ContentType' => $meta['mediatype'],
            'Metadata' => [
                'idplayer' => $idPlayer,
                'idgame' => $idGame
            ],
            'StorageClass' => 'STANDARD',
        ]);

        $em = $this->getDoctrine()->getManager();

        //-- Picture
        $picture = new Picture();
        $picture->setPath($key);
        $picture->setMetadata(serialize($metadata));
        $picture->setPlayer($playerChart->getPlayer());
        $picture->setGame($playerChart->getChart()->getGroup()->getGame());
        $em->persist($picture);

        //-- Proof
        $proof = new Proof();
        $proof->setPicture($picture);
        $em->persist($proof);

        //-- PlayerChart
        $playerChart->setProof($proof);
        if ($playerChart->getStatus()->getIdStatus() === PlayerChartStatus::ID_STATUS_NORMAL) {
            // NORMAL TO NORMAL_SEND_PROOF
            $playerChart->setStatus(
                $this->getDoctrine()->getManager()->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF)
            );
        } else {
            // INVESTIGATION TO DEMAND_SEND_PROOF
            $playerChart->setStatus(
                $this->getDoctrine()->getManager()->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_DEMAND_SEND_PROOF)
            );
        }
        $em->flush();

        $response = new Response();
        $response->setContent(json_encode([
            'id' => $id,
            'file' => $file,
            'meta' => $meta,
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
