<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Player\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Player;

class GetStats extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Player $player
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Player $player, Request $request): mixed
    {
        $idGame = $request->query->get('idGame');

        $qb = $this->em->createQueryBuilder()
            ->select('s', 'COUNT(pc) as nb')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus', 's')
            ->join('s.playerCharts', 'pc')
            ->where('pc.player = :player')
            ->setParameter('player', $player)
            ->groupBy('s.id');

        if ($idGame !== null) {
            $qb->join('pc.chart', 'c')
                ->join('c.group', 'g')
                ->join('g.game', 'game')
                ->andWhere('game.id = :idGame')
                ->setParameter('idGame', (int)$idGame);
        }

        return $qb->getQuery()->getResult();
    }
}