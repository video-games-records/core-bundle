<?php

namespace VideoGamesRecords\CoreBundle\Controller\Team;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Manager\AvatarManager;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

/**
 * Class TeamController
 * @Route("/teams")
 */
class AvatarController extends AbstractController
{
    private TranslatorInterface $translator;
    private AvatarManager $avatarManager;
    private EntityManagerInterface $em;

    private array $extensions = array(
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
    );

    public function __construct(TranslatorInterface $translator, AvatarManager $avatarManager, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->avatarManager = $avatarManager;
        $this->em = $em;
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
        $this->em->flush();

        return new JsonResponse([
            'message' => $this->translator->trans('avatar.success'),
        ], 200);
    }


    /**
     * @Route(path="/{id}/avatar", requirements={"id": "[1-9]\d*"}, name="vgr_core_team_avatar", methods={"GET"})
     * @param Team $team
     * @return StreamedResponse
     * @throws FilesystemException
     */
    public function download(Team $team): StreamedResponse
    {
        $response = $this->avatarManager->read($team->getLogo());
        $response->setPublic();
        $response->setMaxAge(3600);
        return $response;
    }
}
