<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Manager\ScoreManager;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class ScorePlatformController extends AbstractController
{
    private ScoreManager $scoreManager;
    private UserProvider $userProvider;


    public function __construct(
        ScoreManager $scoreManager,
        UserProvider $userProvider
    ) {
        $this->scoreManager = $scoreManager;
        $this->userProvider = $userProvider;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ORMException
     */
    public function update(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $idGame = $data['idGame'];
        $idPlatform = $data['idPlatform'];

        $this->scoreManager->updatePlatform(
            $this->userProvider->getPlayer(),
            $idGame,
            $idPlatform
        );
        return new JsonResponse(['data' => true]);
    }
}
