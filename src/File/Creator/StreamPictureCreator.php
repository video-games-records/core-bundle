<?php

namespace VideoGamesRecords\CoreBundle\File\Creator;

use VideoGamesRecords\CoreBundle\File\Picture;

class StreamPictureCreator extends AbstractCreator
{

    public function createPicture($data): Picture
    {
        $picture = imagecreatefromstring($data);

        return Picture::create($picture);
    }
}
