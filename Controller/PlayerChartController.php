<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Picture;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Exception\AccessDeniedException;
use Aws\S3\S3Client;
use Eko\FeedBundle\Feed\FeedManager;

/**
 * Class PlayerChartController
 * @Route("/player-chart")
 */
class PlayerChartController extends AbstractController
{
    private $userManager;
    private $s3client;
    protected $feedManager;

    private $extensions = array(
        'text/plain' => '.txt',
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
    );

    public function __construct(
        UserManagerInterface $userManager,
        S3Client $s3client,
        FeedManager $feedManager
    ) {
        $this->userManager = $userManager;
        $this->s3client = $s3client;
        $this->feedManager = $feedManager;
    }

    public function getPlayer()
    {
        return $this->getUser()->getRelation();
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
        $em = $this->getDoctrine()->getManager();

        $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->majPlatform(
            $this->getPlayer(),
            $em->getReference(Game::class, $idGame),
            $em->getReference(Platform::class, $idPlatform)
        );
        return new JsonResponse(['data' => true]);
    }

    /**
     * @Route("/top-score", name="playerChart_top_score", methods={"GET"})
     * @Cache(smaxage="10")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rssAction(Request $request)
    {
        $idGame = $request->query->get('idGame', null);
        $idGroup = $request->query->get('idGroup', null);

        $playerCharts = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->rssTopScore($idGame, $idGroup);

        $feed = $this->feedManager->get('player.chart.high.scores');

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
        if (!in_array($playerChart->getStatus()->getId(), PlayerChartStatus::getStatusForProving())) {
            throw new AccessDeniedException('ACESS DENIED');
        }

        $id = $playerChart->getId();
        $idPlayer = $playerChart->getPlayer()->getId();
        $idGame = $playerChart->getChart()->getGroup()->getGame()->getId();

        $data = json_decode($request->getContent(), true);
        $file = $data['file'];

        $hash = hash_file('sha256', $file);
        $picture = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Picture')->findOneBy(
            array(
                'hash' => $hash,
                'player' => $playerChart->getPlayer(),
                'game' => $playerChart->getChart()->getGroup()->getGame(),
            )
        );

        $em = $this->getDoctrine()->getManager();

        if ($picture == null) {
            $fp = fopen($file, 'r');
            $meta = stream_get_meta_data($fp);

            $metadata = [
                'idplayer' => $idPlayer,
                'idgame' => $idGame,
            ];
            $key = $idPlayer . '/' . $idGame . '/'. uniqid() . $this->extensions[$meta['mediatype']];

            $this->s3client->putObject([
                'Bucket' => $_ENV['AWS_BUCKET_PROOF'],
                'Key'    => $key,
                'Body'   => $fp,
                'ACL'    => 'public-read',
                'ContentType' => $meta['mediatype'],
                'Metadata' => [
                    'idplayer' => $idPlayer,
                    'idgame' => $idGame
                ],
                'StorageClass' => 'STANDARD',
            ]);

            //-- Picture
            $picture = new Picture();
            $picture->setPath($key);
            $picture->setMetadata(serialize($metadata));
            $picture->setPlayer($playerChart->getPlayer());
            $picture->setGame($playerChart->getChart()->getGroup()->getGame());
            $picture->setHash($hash);
            $em->persist($picture);
        }


        //-- Proof
        $proof = new Proof();
        $proof->setPicture($picture);
        $em->persist($proof);

        //-- PlayerChart
        $playerChart->setProof($proof);
        if ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL) {
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
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param PlayerChart $playerChart
     * @param Request     $request
     * @return Response
     * @throws AccessDeniedException
     */
    public function sendVideo(PlayerChart $playerChart, Request $request)
    {
        if ($playerChart->getPlayer() != $this->getPlayer()) {
            throw new AccessDeniedException('ACESS DENIED');
        }
        if (!in_array($playerChart->getStatus()->getId(), PlayerChartStatus::getStatusForProving())) {
            throw new AccessDeniedException('ACESS DENIED');
        }

        $id = $playerChart->getId();

        $data = json_decode($request->getContent(), true);
        $url = $data['url'];

        $video = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Video')->findOneBy(
            array(
                'url' => $url,
            )
        );

        $em = $this->getDoctrine()->getManager();

        if ($video == null) {
            //-- Video
            $video = new Video();
            $video->setUrl($url);
            $video->setPlayer($this->getPlayer());
            $video->setGame($playerChart->getChart()->getGroup()->getGame());
            $video->setLibVideo($playerChart->getChart()->getCompleteName('en'));
            $em->persist($video);
        }

        //-- Proof
        $proof = new Proof();
        $proof->setVideo($video);
        $em->persist($proof);

        //-- PlayerChart
        $playerChart->setProof($proof);
        if ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL) {
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
            'url' => $url,
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
