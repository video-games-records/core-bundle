<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartLibRepository;
use VideoGamesRecords\CoreBundle\Tools\Score;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name:'vgr_player_chartlib')]
#[ORM\Entity(repositoryClass: PlayerChartLibRepository::class)]
#[ORM\UniqueConstraint(name: "uniq_player_chart", columns: ["player_chart_id", "chartlib_id"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get()
    ],
    normalizationContext: ['groups' => ['player-chart-lib:read']]
)]
class PlayerChartLib
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'bigint', nullable: false)]
    private string $value;

    #[ORM\ManyToOne(targetEntity: ChartLib::class)]
    #[ORM\JoinColumn(name:'chartlib_id', referencedColumnName:'id', nullable:false)]
    private ChartLib $libChart;

    #[ORM\ManyToOne(targetEntity: PlayerChart::class, inversedBy: 'libs')]
    #[ORM\JoinColumn(name:'player_chart_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private PlayerChart $playerChart;

    private array $parseValue;

    public function __toString()
    {
        return sprintf('%s', Score::formatScore($this->value, $this->getLibChart()->getType()->getMask()));
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setValue($value = null): void
    {
        if ($value != null) {
            $this->value = (string) $value;
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setLibChart(ChartLib $libChart): void
    {
        $this->libChart = $libChart;
    }

    public function getLibChart(): ChartLib
    {
        return $this->libChart;
    }

    public function setPlayerChart(PlayerChart $playerChart): void
    {
        $this->playerChart = $playerChart;
    }

    public function getPlayerChart(): PlayerChart
    {
        return $this->playerChart;
    }

    public function getParseValue(): array
    {
        $this->setParseValueFromValue();
        return $this->parseValue;
    }

    public function setParseValue($parseValue): void
    {
        $this->parseValue = $parseValue;
    }


    public function setParseValueFromValue(): void
    {
        $this->parseValue = Score::getValues(
            $this->getLibChart()
                ->getType()
                ->getMask(),
            $this->value ?? null
        );
    }

    public function setValueFromPaseValue(): void
    {
        if ($this->parseValue == null) {
            $this->value = '';
        } else {
            $this->value = (string) Score::formToBdd(
                $this->getLibChart()
                    ->getType()
                    ->getMask(),
                $this->parseValue
            );
        }
    }

    #[Groups(['player-chart-lib:read'])]
    public function getFormatValue(): string
    {
        return Score::formatScore(
            $this->value,
            $this->getLibChart()
                ->getType()
                ->getMask()
        );
    }
}
