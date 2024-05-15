<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use VideoGamesRecords\CoreBundle\Repository\TeamSerieRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank1Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank2Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank3Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbEqualTrait;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;

#[ORM\Table(name:'vgr_team_serie')]
#[ORM\Entity(repositoryClass: TeamSerieRepository::class)]
class TeamSerie
{
    use NbEqualTrait;
    use RankPointChartTrait;
    use PointChartTrait;
    use RankMedalTrait;
    use ChartRank1Trait;
    use ChartRank2Trait;
    use ChartRank3Trait;
    use PointGameTrait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name:'team_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Team $team;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Serie::class)]
    #[ORM\JoinColumn(name:'serie_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Serie $serie;

    public function setSerie(Serie $serie): void
    {
        $this->serie = $serie;
    }

    public function getSerie(): Serie
    {
        return $this->serie;
    }

    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }
}
