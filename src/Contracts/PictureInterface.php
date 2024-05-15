<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Contracts;

interface PictureInterface
{
    public const MIME_TYPES = [
        'bmp'  => 'image/bmp',
        'gif'  => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'wbmp' => 'image/vnd.wap.wbmp',
        'xbm'  => 'image/x-xbitmap',
    ];

    public const EXTENSIONS = [
        'gd'   => ['generate' => 'imagegd', 'create' => 'imagecreatefromgd'],
        'gd2'  => ['generate' => 'imagegd2', 'create' => 'imagecreatefromgd2'],
        'gif'  => ['generate' => 'imagegif', 'create' => 'imagecreatefromgif'],
        'jpeg' => ['generate' => 'imagejpeg', 'create' => 'imagecreatefromjpeg'],
        'jpg'  => ['generate' => 'imagejpeg', 'create' => 'imagecreatefromjpeg'],
        'png'  => ['generate' => 'imagepng', 'create' => 'imagecreatefrompng'],
        'wbmp' => ['generate' => 'imagewbmp', 'create' => 'imagecreatefromwbmp'],
        'bmp'  => ['generate' => 'imagewbmp', 'create' => 'imagecreatefromwbmp'],
        'xbm'  => ['generate' => 'imagexbm', 'create' => 'imagecreatefromxbm']
    ];
}
