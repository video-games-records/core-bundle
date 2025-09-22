<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Team\Avatar;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Manager\AvatarManager;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class AvatarUpload extends AbstractController
{
    private AvatarManager $avatarManager;
    private UserProvider $userProvider;
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;


    private array $extensions = array(
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
    );

    public function __construct(
        AvatarManager $avatarManager,
        UserProvider $userProvider,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ) {
        $this->avatarManager = $avatarManager;
        $this->userProvider = $userProvider;
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws FilesystemException|ORMException
     */
    public function __invoke(Request $request): Response
    {
        $team = $this->userProvider->getTeam();

        $data = json_decode($request->getContent(), true);
        $file = $data['file'];
        $fp1 = fopen($file, 'r');
        $meta = stream_get_meta_data($fp1);
        $mimeType = $meta['mediatype'];

        $data = explode(',', $file);

        if (!array_key_exists($meta['mediatype'], $this->extensions)) {
            return new JsonResponse(
                [
                'message' => $this->translator->trans('avatar.extension_not_allowed', [], 'VgrCore'),
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
            'message' => $this->translator->trans('avatar.success', [], 'VgrCore'),
        ], 200);
    }
}
