<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Command\RankUpdate;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class PlayerRankUpdateHandler extends AbstractRankUpdateHandler
{
    public function majRankPointChart(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('pointChart' => 'DESC'));
        Ranking::addObjectRank($players);
        $this->em->flush();
    }

    public function majRankPointGame(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('pointGame' => 'DESC'));
        Ranking::addObjectRank($players, 'rankPointGame', array('pointGame'));
        $this->em->flush();
    }

    public function majRankMedal(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC'));
        Ranking::addObjectRank($players, 'rankMedal', array('chartRank0', 'chartRank1', 'chartRank2', 'chartRank3'));
        $this->em->flush();
    }

    public function majRankCup(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('gameRank0' => 'DESC', 'gameRank1' => 'DESC', 'gameRank2' => 'DESC', 'gameRank3' => 'DESC'));
        Ranking::addObjectRank($players, 'rankCup', array('gameRank0', 'gameRank1', 'gameRank2', 'gameRank3'));
        $this->em->flush();
    }

    public function majRankProof(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('nbChartProven' => 'DESC'));
        Ranking::addObjectRank($players, 'rankProof', array('nbChartProven'));
        $this->em->flush();
    }

    public function majRankBadge(): void
    {
        $players = $this->getPlayerRepository()->findBy(array(), array('pointBadge' => 'DESC', 'nbMasterBadge' => 'DESC'));
        Ranking::addObjectRank($players, 'rankBadge', array('pointBadge', 'nbMasterBadge'));
        $this->em->flush();
    }

    private function getPlayerRepository(): EntityRepository
    {
        return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player');
    }
}
