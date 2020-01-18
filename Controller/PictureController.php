<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Entity\Picture;

/**
 * Class PictureController
 * @Route("/proof/picture")
 */
class PictureController extends Controller
{
    /**
     * @Route("/{id}", requirements={"id": "[1-9]\d*"}, name="vgr_core_picture_index", methods={"GET"})
     * @Cache(smaxage="10")
     *
     * @param Picture $picture
     */
    public function indexAction(Picture $picture)
    {
        $s3 = $this->get('aws.s3');
        try {
            $result = $s3->getObject(
                [
                    'Bucket' => $_ENV['AWS_BUCKET_PROOF'],
                    'Key' => $picture->getPath(),
                ]
            );

            // Display the object in the browser.
            header("Content-Type: {$result['ContentType']}");
            echo $result['Body'];
        } catch (S3Exception $e) {

        }
        exit;
    }
}
