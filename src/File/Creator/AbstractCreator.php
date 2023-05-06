<?php

namespace VideoGamesRecords\CoreBundle\File\Creator;

use GdImage;
use VideoGamesRecords\CoreBundle\File\Picture;
use VideoGamesRecords\CoreBundle\File\PictureInterface;

abstract class AbstractCreator implements PictureInterface
{
    /**
     * Note that the Creator may also provide some default implementation of the
     * factory method.
     */
    abstract public function createPicture($data): Picture;
}