<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\DataTransformer\UserToPlayerTransformer;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Service\ScorePlatformManager;

class ScorePlatformController extends AbstractController
{
    private ScorePlatformManager $scorePlatformManager;
    private UserToPlayerTransformer $userToPlayerTransformer;


    public function __construct(
        ScorePlatformManager $scorePlatformManager,
        UserToPlayerTransformer $userToPlayerTransformer
    ) {
        $this->scorePlatformManager = $scorePlatformManager;
        $this->userToPlayerTransformer = $userToPlayerTransformer;
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
        $em = $this->getDoctrine()->getManager();

        $this->scorePlatformManager->updatePlatform(
            $this->userToPlayerTransformer->transform($this->getUser()),
            $em->getReference(Game::class, $idGame),
            $em->getReference(Platform::class, $idPlatform)
        );
        return new JsonResponse(['data' => true]);
    }
}
