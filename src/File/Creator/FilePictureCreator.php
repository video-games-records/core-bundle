<?php

namespace VideoGamesRecords\CoreBundle\File\Creator;

use Exception;
use VideoGamesRecords\CoreBundle\File\Picture;

class FilePictureCreator extends AbstractCreator
{
    public function createPicture($data): Picture
    {
        $file = realpath($data);
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
}
