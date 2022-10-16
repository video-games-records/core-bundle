<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;

/**
 * Class DefaultController
 */
class DefaultController extends AbstractController
{
    /**
     * @return Player|null
     */
    public function getPlayer(): ?Player
    {
        if ($this->getUser() !== null) {
            return $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')
                ->getPlayerFromUser($this->getUser());
        }
        return null;
    }

    /**
     * @return Team|null
     */
    public function getTeam(): ?Team
    {
        if ($this->getUser() !== null) {
            $player =  $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')
                ->getPlayerFromUser($this->getUser());
            return $player->getTeam();
        }
        return null;
    }

    /**
     * @param bool        $success
     * @param string|null $message
     * @return Response
     */
    public function getResponse(bool $success, string $message = null): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode([
            'success' => $success,
            'message' => $message,
        ]));
        return $response;
    }
}
