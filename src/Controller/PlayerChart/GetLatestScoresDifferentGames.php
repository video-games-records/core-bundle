<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;

/**
 * Contrôleur pour récupérer les N derniers scores postés avec des jeux différents
 */
class GetLatestScoresDifferentGames extends AbstractController
{
    private EntityManagerInterface $em;
    private CacheInterface $cache;

    public function __construct(EntityManagerInterface $em, CacheInterface $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }

    /**
     * Récupère les N derniers scores postés en s'assurant que chaque score provient d'un jeu différent
     *
     * @param Request $request
     * @return PlayerChart[]
     */
    public function __invoke(Request $request): array
    {
        // Récupérer le paramètre N depuis la query string (par défaut 10)
        $limit = (int) $request->query->get('limit', 10);

        // Récupérer le paramètre refresh pour vider le cache
        $refresh = $request->query->has('refresh');

        // Valider la limite
        if ($limit <= 0) {
            $limit = 10;
        }
        if ($limit > 20) {
            $limit = 20;
        }

        // Si refresh=1, vider le cache d'abord
        if ($refresh) {
            $this->clearCacheForLimit($limit);
        }

        // Utiliser le cache pour éviter de recalculer la requête
        $cacheKey = "latest_scores_different_games_{$limit}";

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($limit) {
            // Cache pendant 5 minutes (300 secondes)
            $item->expiresAfter(300);

            return $this->getOptimizedLatestScores($limit);
        });
    }

    /**
     * Vide le cache pour une limite spécifique
     */
    private function clearCacheForLimit(int $limit): void
    {
        // Sinon on supprime la clé spécifique
        $cacheKey = "latest_scores_different_games_{$limit}";
        $this->cache->delete($cacheKey);
    }

    /**
     * Requête optimisée pour récupérer les derniers scores (DISTINCT par jeu)
     */
    private function getOptimizedLatestScores(int $limit): array
    {
        // Requête SQL avec ROW_NUMBER pour garantir un seul score par jeu
        $sql = "
            SELECT pc.id
            FROM (
                SELECT 
                    pc_inner.id,
                    pc_inner.last_update,
                    ROW_NUMBER() OVER (
                        PARTITION BY ga_inner.id 
                        ORDER BY pc_inner.last_update DESC, pc_inner.id DESC
                    ) as rn
                FROM vgr_player_chart pc_inner
                INNER JOIN vgr_chart c_inner ON pc_inner.chart_id = c_inner.id
                INNER JOIN vgr_group g_inner ON c_inner.group_id = g_inner.id
                INNER JOIN vgr_game ga_inner ON g_inner.game_id = ga_inner.id
                WHERE pc_inner.last_update >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ) pc
            WHERE pc.rn = 1
            ORDER BY pc.last_update DESC, pc.id DESC
            LIMIT :limit
        ";

        // Exécuter la requête pour récupérer les IDs
        $connection = $this->em->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $result = $stmt->executeQuery();
        $ids = $result->fetchFirstColumn();

        if (empty($ids)) {
            return [];
        }

        // Récupérer les entités complètes avec leurs relations en une seule requête
        $qb = $this->em->createQueryBuilder()
            ->select('pc')
            ->from(PlayerChart::class, 'pc')
            ->innerJoin('pc.chart', 'c')
            ->innerJoin('c.group', 'g')
            ->innerJoin('g.game', 'game')
            ->innerJoin('pc.player', 'p')
            ->leftJoin('pc.status', 's')
            ->leftJoin('pc.platform', 'pl')
            ->addSelect('c', 'g', 'game', 'p', 's', 'pl')
            ->where('pc.id IN (:ids)')
            ->setParameter('ids', $ids);

        // Maintenir l'ordre de la requête SQL
        $results = $qb->getQuery()->getResult();

        // Réordonner selon l'ordre des IDs retournés par la requête SQL
        $orderedResults = [];
        $resultsById = [];

        foreach ($results as $result) {
            $resultsById[$result->getId()] = $result;
        }

        foreach ($ids as $id) {
            if (isset($resultsById[$id])) {
                $orderedResults[] = $resultsById[$id];
            }
        }

        return $orderedResults;
    }
}
