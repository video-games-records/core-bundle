<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Repository\PlayerGroupRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank0Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank1Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank2Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank3Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank4Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank5Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\LastUpdateTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;

#[ORM\Table(name:'vgr_player_group')]
#[ORM\Entity(repositoryClass: PlayerGroupRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get()
    ],
    normalizationContext: ['groups' => ['player-group:read']]
)]
class PlayerGroup
{
    use NbChartTrait;
    use NbChartProvenTrait;
    use RankMedalTrait;
    use ChartRank0Trait;
    use ChartRank1Trait;
    use ChartRank2Trait;
    use ChartRank3Trait;
    use ChartRank4Trait;
    use ChartRank5Trait;
    use RankPointChartTrait;
    use PointChartTrait;
    use LastUpdateTrait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Player $player;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Group::class, fetch: 'EAGER')]
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

    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}
