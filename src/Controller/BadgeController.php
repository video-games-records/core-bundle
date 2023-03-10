<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Badge;

/**
 * Class BadgeController
 * @Route("/badge")
 */
class BadgeController extends AbstractController
{
    private FilesystemOperator $appStorage;

    private string $prefix = 'badge/';

    public function __construct(FilesystemOperator $appStorage)
    {
        $this->appStorage = $appStorage;
    }


    /**
     * @Route(path="/{id}/picture", requirements={"id": "[1-9]\d*"}, name="vgr_core_badge_picture", methods={"GET"})
     * @Cache(smaxage="2592000")
     * @param Badge $badge
     * @return StreamedResponse
     * @throws FilesystemException
     */
    public function pictureAction(Badge $badge): StreamedResponse
    {
        $path = $this->prefix . $badge->getType() . DIRECTORY_SEPARATOR . $badge->getPicture();
        if (!$this->appStorage->fileExists($path)) {
            $path = $this->prefix . 'default.gif';
        }

        $stream = $this->appStorage->readStream($path);
        return new StreamedResponse(function() use ($stream) {
            fpassthru($stream);
        }, 200, ['Content-Type' => 'image/gif']);
    }
}
