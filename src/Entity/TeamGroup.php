<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Repository\TeamGroupRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank0Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank1Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank2Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank3Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;

#[ORM\Table(name:'vgr_team_group')]
#[ORM\Entity(repositoryClass: TeamGroupRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get()
    ],
    normalizationContext: ['groups' => ['team-group:read']]
)]
class TeamGroup
{
    use RankPointChartTrait;
    use PointChartTrait;
    use RankMedalTrait;
    use ChartRank0Trait;
    use ChartRank1Trait;
    use ChartRank2Trait;
    use ChartRank3Trait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name:'team_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Team $team;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name:'group_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Group $group;

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setTeam(Team $team = null): void
    {
        $this->team = $team;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }
}
