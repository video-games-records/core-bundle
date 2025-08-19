<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Player\Game;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerGame;

class GetStats extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Player    $player
     * @return array
     */
    public function __invoke(Player $player): array
    {
        $playerGames = $this->getPlayerGameStats($player);
        $stats = $this->getStatusPerGame($player);

        /** @var PlayerGame $playerGame */
        foreach ($playerGames as $playerGame) {
            if (isset($stats[$playerGame->getGame()->getId()])) {
                $playerGame->setStatuses($stats[$playerGame->getGame()->getId()]);
            }
        }
        return $playerGames;
    }

     /**
     * Return data from player with game and platforms
     *
     * @param $player
     * @return array
     */
    private function getPlayerGameStats($player): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('pg')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerGame', 'pg')
            ->join('pg.game', 'g')
            ->addSelect('g')
            ->join('g.platforms', 'p')
            ->addSelect('p')
            ->where('pg.player = :player')
            ->setParameter('player', $player)
            ->orderBy('g.' . (Locale::getDefault() == 'fr' ? 'libGameFr' : 'libGameEn'), 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $player
     * @return array
     */
    private function getStatusPerGame($player): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('gam.id')
            ->from('VideoGamesRecords\CoreBundle\Entity\Game', 'gam')
            ->addSelect('status.id as idStatus')
            ->addSelect('COUNT(pc) as nb')
            ->innerJoin('gam.groups', 'grp')
            ->innerJoin('grp.charts', 'chr')
            ->innerJoin('chr.playerCharts', 'pc')
            ->innerJoin('pc.status', 'status')
            ->where('pc.player = :player')
            ->setParameter('player', $player)
            ->groupBy('gam.id')
            ->addGroupBy('status.id')
            ->orderBy('gam.id', 'ASC')
            ->addOrderBy('status.id', 'ASC');

        $list = $qb->getQuery()->getResult(2);

        $games = [];
        foreach ($list as $row) {
            $idGame = $row['id'];
            if (!array_key_exists($idGame, $games)) {
                $games[$idGame] = [];
            }
            $games[$idGame][] = [
                'status' => $this->em->find('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus', $row['idStatus']),
                'nb' => $row['nb'],
            ];
        }
        return $games;
    }
}
