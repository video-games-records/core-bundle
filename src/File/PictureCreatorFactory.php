<?php

namespace VideoGamesRecords\CoreBundle\File;

use Exception;
use VideoGamesRecords\CoreBundle\Contracts\PictureInterface;

class PictureCreatorFactory implements PictureInterface
{
    public static function fromFile(string $path): Picture
    {
        $file = realpath($path);
        if ($file === false) {
            throw new Exception('Unable to load picture file. The file does not exists.');
        }

        $extension = mb_strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!array_key_exists($extension, self::EXTENSIONS)) {
            throw new Exception('Unknown extension of file when converting to PHP resource.');
        }

        $method = Picture::getCreateMethod($extension);
        $picture = $method($file);

        return Picture::create($picture);
    }

    public static function fromStream(string $stream): Picture
    {
        $picture = imagecreatefromstring($stream);

        return Picture::create($picture);
    }
}
