<?php

namespace VideoGamesRecords\CoreBundle\Controller\Picture;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Picture;
use VideoGamesRecords\CoreBundle\File\PictureCreatorFactory;

class Show extends AbstractController
{
    private S3Client $s3client;

    public function __construct(S3Client $s3client)
    {
        $this->s3client = $s3client;
    }

    /**
     * @Route(path="/proof/picture/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_core_picture_index", methods={"GET"})
     * @Cache(expires="+30 days")
     * @param Picture $picture
     * @throws Exception
     */
    public function __invoke(Picture $picture): void
    {
        try {
            $result = $this->s3client->getObject(
                [
                    'Bucket' => $_ENV['AWS_S3_BUCKET_PROOF'],
                    'Key' => $picture->getPath(),
                ]
            );

            // Display the object in the browser.
            header("Content-Type: {$result['ContentType']}");
            echo $result['Body'];
        } catch (S3Exception $e) {
            chdir(__DIR__);
            $picture = PictureCreatorFactory::fromFile('../Resources/img/no_photo.gif');
            $picture->showPicture('gif');
        }
        exit;
    }
}
