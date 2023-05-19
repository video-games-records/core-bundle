<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\Repository\GameRepository;

/**
 * Class SerieController
 * @Route("/serie")
 */
class SerieController extends AbstractController
{
    private FilesystemOperator $appStorage;

    private string $prefix = 'series/picture/';

    public function __construct(FilesystemOperator $appStorage)
    {
        $this->appStorage = $appStorage;
    }


    /**
     * @Route(path="/{id}/picture", requirements={"id": "[1-9]\d*"}, name="vgr_core_serie_picture", methods={"GET"})
     * @Cache(expires="+30 days")
     * @param Serie $serie
     * @return StreamedResponse
     * @throws FilesystemException
     */
    public function pictureAction(Serie $serie): StreamedResponse
    {
        $path = $this->prefix . $serie->getPicture();
        if (!$this->appStorage->fileExists($path)) {
            $path = $this->prefix . 'default.png';
        }

        $stream = $this->appStorage->readStream($path);
        return new StreamedResponse(function() use ($stream) {
            fpassthru($stream);
        }, 200, ['Content-Type' => 'image/png']);
    }
}
