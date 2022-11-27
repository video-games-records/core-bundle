<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Tools\Score;

/**
 * PlayerChartLib
 *
 * @ORM\Table(name="vgr_player_chartlib")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerChartLibRepository")
 */
class PlayerChartLib
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;


    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     *
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private ?int $value = null;


    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartLib")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLibChart", referencedColumnName="idLibChart")
     * })
     */
    private ChartLib $libChart;


    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChart", inversedBy="libs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerChart", referencedColumnName="id")
     * })
     */
    private playerChart $playerChart;


    private array $parseValue;
    private string $formatValue;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s', Score::formatScore($this->value, $this->getLibChart()->getType()->getMask()));
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return PlayerChartLib
     */
    public function setId(int $id): PlayerChartLib
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get value
     * @return int|null
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * Set value
     * @param int|null $value
     * @return PlayerChartLib
     */
    public function setValue(int $value = null): PlayerChartLib
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set libChart
     * @param ChartLib|null $libChart
     * @return PlayerChartLib
     */
    public function setLibChart(ChartLib $libChart = null): PlayerChartLib
    {
        $this->libChart = $libChart;
        return $this;
    }

    /**
     * Get lib
     *
     * @return ChartLib
     */
    public function getLibChart(): ChartLib
    {
        return $this->libChart;
    }


    /**
     * Set player
     * @param PlayerChart $playerChart
     * @return PlayerChartLib
     */
    public function setPlayerChart(PlayerChart $playerChart): PlayerChartLib
    {
        $this->playerChart = $playerChart;
        return $this;
    }

    /**
     * Get playerChart
     *
     * @return PlayerChart
     */
    public function getPlayerChart(): PlayerChart
    {
        return $this->playerChart;
    }


    /**
     * Get parseValue
     *
     * @return array
     */
    public function getParseValue(): array
    {
        $this->setParseValueFromValue();
        return $this->parseValue;
    }

    /**
     * Set parseValue
     *
     * @param $parseValue
     * @return PlayerChartLib
     */
    public function setParseValue($parseValue): PlayerChartLib
    {
        $this->parseValue = $parseValue;
        return $this;
    }


    /**
     *
     */
    public function setParseValueFromValue(): void
    {
        $this->parseValue = Score::getValues(
            $this->getLibChart()
                ->getType()
                ->getMask(),
            $this->value
        );
    }


    /**
     *
     */
    public function setValueFromPaseValue(): void
    {
        if ($this->parseValue == null) {
            $this->value = null;
        } else {
            $this->value = (int) Score::formToBdd(
                $this->getLibChart()
                    ->getType()
                    ->getMask(),
                $this->parseValue
            );
        }
    }

    /**
     * @return string
     */
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
