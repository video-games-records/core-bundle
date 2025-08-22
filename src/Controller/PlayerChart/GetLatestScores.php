<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\PlayerChart;

use ApiPlatform\Doctrine\Orm\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;

/**
 * Contrôleur pour récupérer les derniers scores postés (tous, sans distinction de jeux)
 */
class GetLatestScores extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Request $request): Paginator
    {
        // Récupérer les paramètres de pagination
        $days = (int) $request->query->get('days', 7);
        $page = $request->query->getInt('page', 1);
        $itemsPerPage = $request->query->getInt('itemsPerPage', 50);

        $queryBuilder = $this->em->createQueryBuilder()
            ->select('pc')
            ->from(PlayerChart::class, 'pc')
            ->innerJoin('pc.chart', 'c')
            ->innerJoin('c.group', 'g')
            ->innerJoin('g.game', 'game')
            ->innerJoin('pc.player', 'p')
            ->addSelect('c', 'g', 'game', 'p')
            ->orderBy('pc.lastUpdate', 'DESC')
            ->addOrderBy('pc.id', 'DESC');

        // Ajouter la condition sur les jours si spécifiée
        if ($days > 0) {
            $queryBuilder->where('pc.lastUpdate >= :dateLimit')
                ->setParameter('dateLimit', new \DateTime("-{$days} days"));
        }

        // Appliquer la pagination manuellement
        $firstResult = ($page - 1) * $itemsPerPage;
        $queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($itemsPerPage);

        return new Paginator(new DoctrinePaginator($queryBuilder));
    }
}
