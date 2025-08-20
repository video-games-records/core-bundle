<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * Récupère les derniers scores postés ordonnés par lastUpdate DESC
     *
     * @param Request $request
     * @return PlayerChart[]
     */
    public function __invoke(Request $request): array
    {
        // Récupérer les paramètres depuis la query string
        $days = (int) $request->query->get('days', 7);

        // Valider les paramètres
        if ($days < 0) {
            $days = 7;
        }

        return $this->getLatestScores($days);
    }

    /**
     * Requête simple pour récupérer les derniers scores
     */
    private function getLatestScores(int $days): array
    {
        $qb = $this->em->createQueryBuilder()
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
            $qb->where('pc.lastUpdate >= :dateLimit')
                ->setParameter('dateLimit', new \DateTime("-{$days} days"));
        }

        return $qb->getQuery()->getResult();
    }
}
