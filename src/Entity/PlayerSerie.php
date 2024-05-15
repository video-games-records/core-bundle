<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Repository\PlayerSerieRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank0Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank1Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank2Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank3Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank4Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank5Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;

#[ORM\Table(name:'vgr_player_serie')]
#[ORM\Entity(repositoryClass: PlayerSerieRepository::class)]
class PlayerSerie
{
    use RankMedalTrait;
    use ChartRank0Trait;
    use ChartRank1Trait;
    use ChartRank2Trait;
    use ChartRank3Trait;
    use ChartRank4Trait;
    use ChartRank5Trait;
    use RankPointChartTrait;
    use PointChartTrait;
    use NbChartTrait;
    use NbChartWithoutDlcTrait;
    use NbChartProvenTrait;
    use NbChartProvenWithoutDlcTrait;
    use NbGameTrait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Player $player;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Serie::class)]
    #[ORM\JoinColumn(name:'serie_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Serie $serie;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointChartWithoutDlc;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointGame;

    public function setPointChartWithoutDlc(int $pointChartWithoutDlc): void
    {
        $this->pointChartWithoutDlc = $pointChartWithoutDlc;
    }

    public function getPointChartWithoutDlc(): int
    {
        return $this->pointChartWithoutDlc;
    }

    public function setPointGame(int $pointGame): void
    {
        $this->pointGame = $pointGame;
    }

    public function getPointGame(): int
    {
        return $this->pointGame;
    }

    public function setSerie(Serie $serie = null): void
    {
        $this->serie = $serie;
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getMedalsBackgroundColor(): string
    {
        $class = [
            0 => '',
            1 => 'bg-first',
            2 => 'bg-second',
            3 => 'bg-third',
        ];

        if ($this->getRankMedal() <= 3) {
            return sprintf('class="%s"', $class[$this->getRankMedal()]);
        }

        return '';
    }
}
