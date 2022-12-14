<?php

namespace VideoGamesRecords\CoreBundle\Controller\Team;

use League\Flysystem\FilesystemException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;
use VideoGamesRecords\CoreBundle\Service\Team\AvatarManager;

/**
 * Class TeamController
 * @Route("/teams")
 */
class AvatarController extends AbstractController
{
    private TranslatorInterface $translator;
    private TeamRepository $teamRepository;
    private AvatarManager $avatarManager;

    private array $extensions = array(
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
    );

    public function __construct(TranslatorInterface $translator, TeamRepository $teamRepository, AvatarManager $avatarManager)
    {
        $this->translator = $translator;
        $this->teamRepository = $teamRepository;
        $this->avatarManager = $avatarManager;
    }

    /**
     * @param Team    $team
     * @param Request $request
     * @return Response
     * @throws FilesystemException
     */
    public function upload(Team $team, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $file = $data['file'];
        $fp1 = fopen($file, 'r');
        $meta = stream_get_meta_data($fp1);
        $mimeType = $meta['mediatype'];

        $data = explode(',', $file);

        if (!array_key_exists($meta['mediatype'], $this->extensions)) {
            return new JsonResponse([
                'message' => $this->translator->trans('avatar.extension_not_allowed'),
            ],
            400
            );
        }

        // Set filename
        $filename = $team->getId() . '_' . uniqid() . '.' . $this->avatarManager->getExtension($mimeType);

        $this->avatarManager->write($filename, base64_decode($data[1]));

        // Save avatar
        $team->setLogo($filename);
        $this->teamRepository->flush();

        return new JsonResponse([
            'message' => $this->translator->trans('avatar.success'),
        ], 200);
    }


    /**
     * @Route(path="/{id}/avatar", requirements={"id": "[1-9]\d*"}, name="vgr_core_team_avatar", methods={"GET"})
     * @Cache(smaxage="30")
     * @param Team $team
     * @return StreamedResponse
     * @throws FilesystemException
     */
    public function download(Team $team): StreamedResponse
    {
        return $this->avatarManager->read($team->getLogo());
    }
}
