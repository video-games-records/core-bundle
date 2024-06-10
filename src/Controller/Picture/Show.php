<?php

namespace VideoGamesRecords\CoreBundle\Controller\Picture;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Picture;

class Show extends AbstractController
{
    private FilesystemOperator $proofStorage;

    private array $extensions = array(
        'png' => 'image/png',
        'jpg' => 'image/jpeg'
    );


    public function __construct(FilesystemOperator $proofStorage)
    {
        $this->proofStorage = $proofStorage;
    }

    /**
     * @Route(path="/proof/picture/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_core_picture_index", methods={"GET"})
     * @Cache(expires="+30 days")
     * @param Picture $picture
     * @return StreamedResponse
     * @throws FilesystemException
     */
    public function __invoke(Picture $picture): StreamedResponse
    {
        $stream = $this->proofStorage->readStream($picture->getPath());
        return new StreamedResponse(function() use ($stream) {
            fpassthru($stream);
            exit();
        }, 200, ['Content-Type' => $this->getMimeType($picture->getPath())]);
    }

    private function getMimeType(string $file): string
    {
        $infos = pathinfo($file);
        return $this->extensions[$infos['extension']] ?? 'image/png';
    }
}
