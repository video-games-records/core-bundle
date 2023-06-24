<?php

namespace VideoGamesRecords\CoreBundle\Controller\Team\Avatar;

use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Manager\AvatarManager;

/**
 * @Route("/teams")
 */
class Download extends AbstractController
{
    private AvatarManager $avatarManager;


    public function __construct(AvatarManager $avatarManager)
    {
        $this->avatarManager = $avatarManager;
    }

    /**
     * @Route(path="/{id}/avatar", requirements={"id": "[1-9]\d*"}, name="vgr_core_team_avatar", methods={"GET"})
     * @param Team $team
     * @return StreamedResponse
     * @throws FilesystemException
     */
    public function __invoke(Team $team): StreamedResponse
    {
        $response = $this->avatarManager->read($team->getLogo());
        $response->setPublic();
        $response->setMaxAge(3600);
        return $response;
    }
}
