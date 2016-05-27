<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Class SubmitController
 * @Route("/submit")
 */
class SubmitController extends Controller
{


    /**
     * @Route("/index", requirements={"id": "[1-9]\d*"}, name="vgr_submit_index")
     * @Method("POST")
     * @Cache(smaxage="10")
     */
    public function indexAction()
    {

        exit;
    }

}
