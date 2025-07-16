<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Repository\PlayerGameRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank0Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank1Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank2Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank3Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank4Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ChartRank5Trait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Game\GameMethodsTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbEqualTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerMethodsTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\PointChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;

#[ORM\Table(name:'vgr_player_game')]
#[ORM\Index(name: "idx_last_update", columns: ["player", "last_update"])]
#[ORM\Entity(repositoryClass: PlayerGameRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get()
    ],
    normalizationContext: ['groups' => ['player-game:read']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'game' => 'exact',
        'game.platforms' => 'exact',
        'game.badge' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'lastUpdate' => 'DESC',
        'rankPointChart' => 'ASC',
        'chartRank0' => 'DESC',
        'chartRank1' => 'DESC',
        'chartRank2' => 'DESC',
        'chartRank3' => 'DESC',
        'pointGame' => 'DESC',
        'nbChart' => 'DESC',
        'nbEqual' => 'ASC',
        'game.nbPlayer' => 'DESC',
        'game.libGameEn' => 'ASC',
        'game.libGameFr' => 'ASC',
    ]
)]
#[ApiResource(
    uriTemplate: '/players/{id}/games',
    operations: [ new GetCollection() ],
    uriVariables: [
        'id' => new Link(toProperty: 'player', fromClass: Player::class),
    ],
    normalizationContext: ['groups' =>
        ['player-game:read', 'player-game:game', 'game:read', 'game:platforms', 'platform:read']
    ],
    order: ['lastUpdate' => 'DESC'],
    paginationEnabled: false,
)]
class PlayerGame
{
    use NbChartTrait;
    use NbChartWithoutDlcTrait;
    use NbChartProvenTrait;
    use NbChartProvenWithoutDlcTrait;
    use NbEqualTrait;
    use RankMedalTrait;
    use ChartRank0Trait;
    use ChartRank1Trait;
    use ChartRank2Trait;
    use ChartRank3Trait;
    use ChartRank4Trait;
    use ChartRank5Trait;
    use RankPointChartTrait;
    use PointChartTrait;
    use PlayerMethodsTrait;
    use GameMethodsTrait;

    #[ApiProperty(identifier: false)]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Player::class, inversedBy: 'playerGame')]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Player $player;

    #[ApiProperty(identifier: false)]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'playerGame', fetch: 'EAGER')]
    #[ORM\JoinColumn(name:'game_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Game $game;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointChartWithoutDlc = 0;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $pointGame = 0;

    #[ORM\Column(nullable: false)]
    private DateTime $lastUpdate;

    private $statuses;


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

    public function setLastUpdate(DateTime $lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    public function getLastUpdate(): DateTime
    {
        return $this->lastUpdate;
    }

    public function setStatuses($statuses): void
    {
        $this->statuses = $statuses;
    }

    public function getStatuses()
    {
        return $this->statuses;
    }

    #[ApiProperty(identifier: true)]
    public function getId(): string
    {
        return sprintf('player=%d;game=%d', $this->player->getId(), $this->game->getId());
    }
}
