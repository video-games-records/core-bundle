<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Block\Service;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use VideoGamesRecords\CoreBundle\ValueObject\GameStatus;

class GameStatusBlockService extends AbstractBlockService
{
    public function __construct(Environment $templating, private readonly EntityManagerInterface $em)
    {
        parent::__construct($templating);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Game Status Block';
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null $response
     * @return Response
     */
    public function execute(
        BlockContextInterface $blockContext,
        ?Response $response = null
    ): Response {
        $settings = $blockContext->getSettings();

        // Statuts dans l'ordre demandé
        $statusOrder = [
            GameStatus::CREATED,
            GameStatus::ADD_SCORE,
            GameStatus::ADD_PICTURE,
            GameStatus::COMPLETED
        ];

        $gamesByStatus = [];
        $totalByStatus = [];
        $totalGames = 0;

        // Récupération des jeux pour chaque statut
        foreach ($statusOrder as $status) {
            $query = $this->em->createQueryBuilder()
                ->from('VideoGamesRecords\CoreBundle\Entity\Game', 'game')
                ->select('game')
                ->where('game.status = :status')
                ->setParameter('status', $status);

            $games = $query->getQuery()->getResult();
            $count = count($games);

            $gamesByStatus[$status] = $games;
            $totalByStatus[$status] = $count;
            $totalGames += $count;
        }

        // Statistiques supplémentaires
        $stats = $this->getGameStatistics($statusOrder);

        return $this->renderResponse(
            '@VideoGamesRecordsCore/Admin/Block/games_by_status.html.twig',
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'gamesByStatus' => $gamesByStatus,
                'totalByStatus' => $totalByStatus,
                'totalGames' => $totalGames,
                'statusOrder' => $statusOrder,
                'stats' => $stats,
            ],
            $response
        );
    }

    /**
     * Récupère des statistiques supplémentaires sur les jeux
     */
    private function getGameStatistics(array $statusOrder): array
    {
        // Pourcentage de progression pour chaque statut
        $totalGamesQuery = $this->em->createQueryBuilder()
            ->from('VideoGamesRecords\CoreBundle\Entity\Game', 'game')
            ->select('COUNT(game.id)')
            ->where('game.status IN (:statuses)')
            ->setParameter('statuses', $statusOrder);

        $totalGames = (int) $totalGamesQuery->getQuery()->getSingleScalarResult();

        $percentages = [];
        if ($totalGames > 0) {
            foreach ($statusOrder as $status) {
                $statusQuery = $this->em->createQueryBuilder()
                    ->from('VideoGamesRecords\CoreBundle\Entity\Game', 'game')
                    ->select('COUNT(game.id)')
                    ->where('game.status = :status')
                    ->setParameter('status', $status);

                $count = (int) $statusQuery->getQuery()->getSingleScalarResult();
                $percentages[$status] = round(($count / $totalGames) * 100, 1);
            }
        }

        // Dernier jeu modifié par statut
        $lastModified = [];
        foreach ($statusOrder as $status) {
            $lastGameQuery = $this->em->createQueryBuilder()
                ->from('VideoGamesRecords\CoreBundle\Entity\Game', 'game')
                ->select('game.libGameEn', 'game.updatedAt')
                ->where('game.status = :status')
                ->andWhere('game.updatedAt IS NOT NULL')
                ->setParameter('status', $status)
                ->orderBy('game.updatedAt', 'DESC')
                ->setMaxResults(1);

            $lastGame = $lastGameQuery->getQuery()->getOneOrNullResult();
            $lastModified[$status] = $lastGame;
        }

        return [
            'percentages' => $percentages,
            'lastModified' => $lastModified,
            'total' => $totalGames,
        ];
    }
}
