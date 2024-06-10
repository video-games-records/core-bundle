<?php

namespace VideoGamesRecords\CoreBundle\Controller\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VideoGamesRecords\CoreBundle\Entity\Picture;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use VideoGamesRecords\CoreBundle\Exception\AccessDeniedException;
use VideoGamesRecords\CoreBundle\File\PictureCreatorFactory;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class SendPicture extends AbstractController
{
    private FilesystemOperator $proofStorage;
    private UserProvider $userProvider;
    private EntityManagerInterface $em;

    private array $extensions = array(
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
    );

    public function __construct(
        FilesystemOperator $proofStorage,
        UserProvider $userProvider,
        EntityManagerInterface $em
    ) {
        $this->proofStorage = $proofStorage;
        $this->userProvider = $userProvider;
        $this->em = $em;
    }

    /**
     * @param PlayerChart $playerChart
     * @param Request     $request
     * @return Response
     * @throws Exception
     */
    public function __invoke(PlayerChart $playerChart, Request $request): Response
    {
        $player = $this->userProvider->getPlayer();

        if ($playerChart->getPlayer() !== $player) {
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
        $picture = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Picture')->findOneBy(
            array(
                'hash' => $hash,
                'player' => $playerChart->getPlayer(),
                'game' => $playerChart->getChart()->getGroup()->getGame(),
            )
        );

        if ($picture == null) {
            $fp = fopen($file, 'r');
            $meta = stream_get_meta_data($fp);
            $data = explode(',', $file);

            // Set filename
            $filename = $idPlayer . '/' . $idGame . '/' . uniqid() . '.' . $this->extensions[$meta['mediatype']];

            $metadata = [
                'idplayer' => $idPlayer,
                'idgame' => $idGame,
            ];

            $pictureFile = PictureCreatorFactory::fromStream(base64_decode($data[1]));
            $pictureFile->scale(1600, 1080);

            /*ob_start();
            imagepng($pictureFile->getImage());
            $imageData = ob_get_clean();*/

            $this->proofStorage->write($filename, $pictureFile->getStream($this->extensions[$meta['mediatype']]));

            //-- Picture
            $picture = new Picture();
            $picture->setPath($filename);
            $picture->setMetadata(serialize($metadata));
            $picture->setPlayer($playerChart->getPlayer());
            $picture->setGame($playerChart->getChart()->getGroup()->getGame());
            $picture->setHash($hash);
            $this->em->persist($picture);
        }


        //-- Proof
        $proof = new Proof();
        $proof->setPicture($picture);
        $proof->setPlayer($playerChart->getPlayer());
        $proof->setChart($playerChart->getChart());
        $this->em->persist($proof);

        //-- PlayerChart
        $playerChart->setProof($proof);
        if ($playerChart->getStatus()->getId() === PlayerChartStatus::ID_STATUS_NORMAL) {
            // NORMAL TO NORMAL_SEND_PROOF
            $playerChart->setStatus(
                $this->em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_NORMAL_SEND_PROOF)
            );
        } else {
            // INVESTIGATION TO DEMAND_SEND_PROOF
            $playerChart->setStatus(
                $this->em->getReference(PlayerChartStatus::class, PlayerChartStatus::ID_STATUS_DEMAND_SEND_PROOF)
            );
        }
        $this->em->flush();

        return new JsonResponse([
            'id' => $id,
            'file' => $file,
        ], 200);
    }
}
