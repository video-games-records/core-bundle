<?php

namespace VideoGamesRecords\CoreBundle\Controller\PlayerChart;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Manager\ScoreManager;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class UpdatePlatform extends AbstractController
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
