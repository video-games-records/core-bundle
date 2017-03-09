<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class VgrBaseController
 */
class VgrBaseController extends Controller
{
    /** @var \VideoGamesRecords\CoreBundle\Entity\Player */
    private $player;

    /**
     * Gets player if any in session
     *
     * @return \VideoGamesRecords\CoreBundle\Entity\Player
     */
    protected function getPlayer()
    {
        if (null !== $this->player) {
            return $this->player;
        }

        if ($this->get('session')->has('vgr_player')) {
            return $this->player = $this->get('session')->get('vgr_player');
        }
        return null;
    }
}
