<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;

/**
 * Class BadgeController
 * @Route("/badge")
 */
class BadgeController extends AbstractController implements BadgeInterface
{
    private FilesystemOperator $appStorage;

    public function __construct(FilesystemOperator $appStorage)
    {
        $this->appStorage = $appStorage;
    }


    /**
     * @Route(path="/{id}/picture", requirements={"id": "[1-9]\d*"}, name="vgr_core_badge_picture", methods={"GET"})
     * @Cache(expires="+30 days")
     * @param Badge $badge
     * @return StreamedResponse
     * @throws FilesystemException
     */
    public function pictureAction(Badge $badge): StreamedResponse
    {
        $path = $this->getDirectory($badge->getType()) . DIRECTORY_SEPARATOR . $badge->getPicture();
        if (!$this->appStorage->fileExists($path)) {
            $path = self::DIRECTORY_DEFAULT . DIRECTORY_SEPARATOR . 'default.gif';
        }

        $stream = $this->appStorage->readStream($path);
        return new StreamedResponse(function() use ($stream) {
            fpassthru($stream);
        }, 200, ['Content-Type' => 'image/gif']);
    }

    /**
     * @param string $type
     * @return string
     */
    private function getDirectory(string $type): string
    {
        if (array_key_exists($type, self::DIRECTORIES)) {
            return self::DIRECTORIES[$type];
        }
        return self::DIRECTORY_DEFAULT . DIRECTORY_SEPARATOR . $type;
    }
}
