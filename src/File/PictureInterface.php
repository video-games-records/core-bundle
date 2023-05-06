<?php
namespace VideoGamesRecords\CoreBundle\File;

interface PictureInterface
{
    const MIME_TYPES = [
        'bmp'  => 'image/bmp',
        'gif'  => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'wbmp' => 'image/vnd.wap.wbmp',
        'xbm'  => 'image/x-xbitmap',
    ];

    const EXTENSIONS = [
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