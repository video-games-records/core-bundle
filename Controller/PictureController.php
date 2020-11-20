<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Picture;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use VideoGamesRecords\CoreBundle\File\Picture as PictureFile;
use Exception;

/**
 * Class PictureController
 * @Route("/proof/picture")
 */
class PictureController extends AbstractController
{
    private $s3client;

    public function __construct(S3Client $s3client)
    {
        $this->s3client = $s3client;
    }

    /**
     * @Route("/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_core_picture_index", methods={"GET"})
     * @Cache(smaxage="10")
     * @param Picture $picture
     * @throws Exception
     */
    public function indexAction(Picture $picture)
    {
        try {
            $result = $this->s3client->getObject(
                [
                    'Bucket' => $_ENV['AWS_BUCKET_PROOF'],
                    'Key' => $picture->getPath(),
                ]
            );

            // Display the object in the browser.
            header("Content-Type: {$result['ContentType']}");
            echo $result['Body'];
        } catch (S3Exception $e) {
            chdir(__DIR__);
            $picture = PictureFile::loadFile('../Resources/img/no_photo.gif');
            $picture->showPicture('png');
        }
        exit;
    }
}
