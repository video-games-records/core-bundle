<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Repository\PlayerPlatformRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;

#[ORM\Table(name:'vgr_player_platform')]
#[ORM\Entity(repositoryClass: PlayerPlatformRepository::class)]
class PlayerPlatform
{
    use NbChartTrait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Player $player;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Platform::class, inversedBy: 'playerPlatform', fetch: 'EAGER')]
    #[ORM\JoinColumn(name:'platform_id', referencedColumnName:'id', nullable:false)]
    private Platform $platform;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $rankPointPlatform;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointPlatform = 0;

    public function setRankPointPlatform(int $rankPointPlatform): void
    {
        $this->rankPointPlatform = $rankPointPlatform;
    }

    public function getRankPointPlatform(): int
    {
        return $this->rankPointPlatform;
    }

    public function setPointPlatform(int $pointPlatform): void
    {
        $this->pointPlatform = $pointPlatform;
    }

    public function getPointPlatform(): int
    {
        return $this->pointPlatform;
    }

    public function setPlatform(Platform $platform): void
    {
        $this->platform = $platform;
    }

    public function getPlatform(): Platform
    {
        return $this->platform;
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
