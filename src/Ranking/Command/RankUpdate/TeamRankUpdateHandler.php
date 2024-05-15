<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Command\RankUpdate;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

class TeamRankUpdateHandler extends AbstractRankUpdateHandler
{
    public function majRankPointChart(): void
    {
        $teams = $this->getTeamRepository()->findBy(array(), array('pointChart' => 'DESC'));
        Ranking::addObjectRank($teams);
        $this->em->flush();
    }

    public function majRankPointGame(): void
    {
        $teams = $this->getTeamRepository()->findBy(array(), array('pointGame' => 'DESC'));
        Ranking::addObjectRank($teams, 'rankPointGame', array('pointGame'));
        $this->em->flush();
    }

    public function majRankMedal(): void
    {
        $teams = $this->getTeamRepository()->findBy(array(), array('chartRank0' => 'DESC', 'chartRank1' => 'DESC', 'chartRank2' => 'DESC', 'chartRank3' => 'DESC'));
        Ranking::addObjectRank($teams, 'rankMedal', array('chartRank0', 'chartRank1', 'chartRank2', 'chartRank3'));
        $this->em->flush();
    }

    public function majRankCup(): void
    {
        $teams = $this->getTeamRepository()->findBy(array(), array('gameRank0' => 'DESC', 'gameRank1' => 'DESC', 'gameRank2' => 'DESC', 'gameRank3' => 'DESC'));
        Ranking::addObjectRank($teams, 'rankCup', array('gameRank0', 'gameRank1', 'gameRank2', 'gameRank3'));
        $this->em->flush();
    }

    public function majRankBadge(): void
    {
        $teams = $this->getTeamRepository()->findBy(array(), array('pointBadge' => 'DESC', 'nbMasterBadge' => 'DESC'));
        Ranking::addObjectRank($teams, 'rankBadge', array('pointBadge', 'nbMasterBadge'));
        $this->em->flush();
    }


    public function majRankProof(): void
    {
        // TODO: Implement majRankProof() method.
    }

    private function getTeamRepository(): EntityRepository
    {
        return $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Team');
    }
}
