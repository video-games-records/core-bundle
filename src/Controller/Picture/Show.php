<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Picture;

use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
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

    #[Route(
        '/picture/{id}/show',
        name: 'vgr_core_picture_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    #[Cache(maxage: 3600 * 24, public: true, mustRevalidate: true)]
    public function __invoke(Picture $picture): StreamedResponse
    {
        $stream = $this->proofStorage->readStream($picture->getPath());
        return new StreamedResponse(function () use ($stream) {
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
