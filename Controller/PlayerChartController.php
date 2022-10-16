<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Picture;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Exception\AccessDeniedException;
use Aws\S3\S3Client;
use VideoGamesRecords\CoreBundle\Service\PlayerChartService;

/**
 * Class PlayerChartController
 * @Route("/player-chart")
 */
class PlayerChartController extends DefaultController
{
    private Security $security;
    private S3Client $s3client;
    private TranslatorInterface $translator;
    private PlayerChartService $playerChartService;

    private array $extensions = array(
        'text/plain' => '.txt',
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
    );

    public function __construct(
        Security $security,
        S3Client $s3client,
        TranslatorInterface $translator,
        PlayerChartService $playerChartService
    ) {
        $this->security = $security;
        $this->s3client = $s3client;
        $this->translator = $translator;
        $this->playerChartService = $playerChartService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function majPlatform(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $idGame = $data['idGame'];
        $idPlatform = $data['idPlatform'];
        $em = $this->getDoctrine()->getManager();

        $this->playerChartService->majPlatform(
            $this->getPlayer(),
            $em->getReference(Game::class, $idGame),
            $em->getReference(Platform::class, $idPlatform)
        );
        return new JsonResponse(['data' => true]);
    }

    /**
     * @param PlayerChart $playerChart
     * @param Request     $request
     * @return Response
     * @throws Exception
     */
    public function sendPicture(PlayerChart $playerChart, Request $request): Response
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
        $picture = $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Picture')->findOneBy(
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
        $proof->setPlayer($playerChart->getPlayer());
        $proof->setChart($playerChart->getChart());
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
    public function sendVideo(PlayerChart $playerChart, Request $request): Response
    {
        if ($playerChart->getPlayer() != $this->getPlayer()) {
            throw new AccessDeniedException('ACESS DENIED');
        }
        if (!in_array($playerChart->getStatus()->getId(), PlayerChartStatus::getStatusForProving())) {
            throw new AccessDeniedException('ACESS DENIED');
        }

        $data = json_decode($request->getContent(), true);
        $url = $data['url'];

        $videoIn = new Video();
        $videoIn->setUrl($url);

        if (in_array($videoIn->getType(), array(Video::TYPE_TWITCH, Video::TYPE_YOUTUBE))) {
            $video = $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Video')->findOneBy(
                array(
                    'videoId' => $videoIn->getVideoId(),
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
            $proof->setPlayer($playerChart->getPlayer());
            $proof->setChart($playerChart->getChart());
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

        } else {
            return $this->getResponse(false, $this->translator->trans('video.type_not_found'));
        }

        return $this->getResponse(true, ($this->translator->trans('proof.form.success')));
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function last(Request $request)
    {
        $locale = $request->getLocale();
        return $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerChart')->getLast($locale);
    }
}
