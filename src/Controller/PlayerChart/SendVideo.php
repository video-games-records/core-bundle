<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Proof;
use VideoGamesRecords\CoreBundle\Entity\Video;
use VideoGamesRecords\CoreBundle\Exception\AccessDeniedException;
use VideoGamesRecords\CoreBundle\Security\UserProvider;
use VideoGamesRecords\CoreBundle\ValueObject\VideoType;

class SendVideo extends AbstractController
{
    private UserProvider $userProvider;
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;

    public function __construct(
        UserProvider $userProvider,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ) {
        $this->userProvider = $userProvider;
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @param PlayerChart $playerChart
     * @param Request     $request
     * @return Proof
     * @throws AccessDeniedException|ORMException
     */
    public function __invoke(PlayerChart $playerChart, Request $request): Proof
    {
        $player = $this->userProvider->getPlayer();

        if ($playerChart->getPlayer() !== $player) {
            throw new AccessDeniedException('ACESS DENIED');
        }
        if (!in_array($playerChart->getStatus()->getId(), PlayerChartStatus::getStatusForProving())) {
            throw new AccessDeniedException('ACESS DENIED');
        }

        $data = json_decode($request->getContent(), true);
        $url = $data['url'];

        $videoIn = new Video();
        $videoIn->setUrl($url);

        if (in_array($videoIn->getType(), array(VideoType::TWITCH, VideoType::YOUTUBE))) {
            $video = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Video')->findOneBy(
                array(
                    'externalId' => $videoIn->getExternalId(),
                )
            );

            if ($video == null) {
                //-- Video
                $video = new Video();
                $video->setUrl($url);
                $video->setPlayer($player);
                $video->setGame($playerChart->getChart()->getGroup()->getGame());
                $this->em->persist($video);
            }

            //-- Proof
            $proof = new Proof();
            $proof->setVideo($video);
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
        } else {
            throw new BadRequestException($this->translator->trans('video.type_not_found', [], 'VgrCore'));
        }

        return $proof;
    }
}
