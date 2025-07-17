<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Badge;

use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;

class GetPicture extends AbstractController implements BadgeInterface
{
    private FilesystemOperator $appStorage;

    public function __construct(FilesystemOperator $appStorage)
    {
        $this->appStorage = $appStorage;
    }

    #[Route(
        '/badge/{id}/picture',
        name: 'vgr_core_badge_picture',
        methods: ['GET'],
        requirements: ['id' => '[1-9]\d*']
    )]
    #[Cache(public: true, maxage: 3600 * 24, mustRevalidate: true)]
    public function __invoke(Badge $badge): StreamedResponse
    {
        $path = $this->getDirectory($badge->getType()->value) . DIRECTORY_SEPARATOR . $badge->getPicture();
        if (!$this->appStorage->fileExists($path)) {
            $path = self::DIRECTORY_DEFAULT . DIRECTORY_SEPARATOR . 'default.gif';
        }

        $stream = $this->appStorage->readStream($path);
        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, ['Content-Type' => 'image/gif']);
    }

    private function getDirectory(string $type): string
    {
        if (array_key_exists($type, self::DIRECTORIES)) {
            return self::DIRECTORIES[$type];
        }
        return self::DIRECTORY_DEFAULT . DIRECTORY_SEPARATOR . $type;
    }
}
