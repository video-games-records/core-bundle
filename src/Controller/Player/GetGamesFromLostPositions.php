<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Player;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use VideoGamesRecords\CoreBundle\Entity\Player;

#[AsController]
class GetGamesFromLostPositions extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Player $player): array
    {
        $query = $this->entityManager->createQuery('
            SELECT DISTINCT g
            FROM VideoGamesRecords\CoreBundle\Entity\Game g
            INNER JOIN VideoGamesRecords\CoreBundle\Entity\Group gr WITH gr.game = g
            INNER JOIN VideoGamesRecords\CoreBundle\Entity\Chart c WITH c.group = gr
            INNER JOIN VideoGamesRecords\CoreBundle\Entity\LostPosition lp WITH lp.chart = c
            WHERE lp.player = :player
            ORDER BY g.libGameEn ASC
        ');

        $query->setParameter('player', $player);
        return $query->getResult();
    }
}
