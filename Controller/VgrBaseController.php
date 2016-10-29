<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class VgrBaseController
 */
class VgrBaseController extends Controller
{
    /**
     * @inheritDoc
     * @return \VideoGamesRecords\CoreBundle\Entity\Player
     */
    protected function getUser()
    {
        return $this->get('session')->get('vgr_player');
    }
}
