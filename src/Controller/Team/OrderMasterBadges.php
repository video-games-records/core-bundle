<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Team;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Repository\TeamBadgeRepository;

class OrderMasterBadges extends AbstractController
{
    public function __construct(
        private readonly TeamBadgeRepository $teamBadgeRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(Team $team, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }

        foreach ($data as $item) {
            if (!isset($item['id']) || !isset($item['mbOrder'])) {
                return $this->json(['error' => 'Missing id or mbOrder in item'], 400);
            }

            $teamBadge = $this->teamBadgeRepository->find($item['id']);

            if (!$teamBadge) {
                return $this->json(['error' => 'TeamBadge not found'], 404);
            }

            if ($teamBadge->getTeam()->getId() !== $team->getId()) {
                return $this->json(['error' => 'TeamBadge does not belong to this team'], 403);
            }

            $teamBadge->setMbOrder((int) $item['mbOrder']);
            $this->entityManager->persist($teamBadge);
        }

        $this->entityManager->flush();

        return $this->json(['success' => true]);
    }
}
