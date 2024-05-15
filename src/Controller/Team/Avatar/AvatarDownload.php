<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Team\Avatar;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Manager\AvatarManager;

class AvatarDownload extends AbstractController
{
    private AvatarManager $avatarManager;


    public function __construct(AvatarManager $avatarManager)
    {
        $this->avatarManager = $avatarManager;
    }

    #[Route(
        '/teams/{id}/avatar',
        name: 'vgr_core_team_avatar',
        methods: ['GET'],
        requirements: ['id' => '[1-9]\d*']
    )]
    public function __invoke(Team $team): StreamedResponse
    {
        $response = $this->avatarManager->read($team->getLogo());
        $response->setPublic();
        $response->setMaxAge(3600);
        return $response;
    }
}
