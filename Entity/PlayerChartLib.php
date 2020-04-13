<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Tools\Score;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PlayerChartLib
 *
 * @ORM\Table(name="vgr_player_chartlib", indexes={@ORM\Index(name="idxIdLibChart", columns={"idLibChart"}), @ORM\Index(name="idxIdPlayer", columns={"idPlayer"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerChartLibRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PlayerChartLib
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var integer
     * @Assert\NotNull
     * @Assert\NotBlank
     *
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private $value;


    /**
     * @var ChartLib
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartLib")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLibChart", referencedColumnName="idLibChart")
     * })
     */
    private $libChart;


    /**
     * @var playerChart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChart", inversedBy="libs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerChart", referencedColumnName="id")
     * })
     */
    private $playerChart;


    private $parseValue;
    private $formatValue;

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
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get idP
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set lib
     *
     * @param ChartLib $libChart
     * @return $this
     */
    public function setLibChart(ChartLib $libChart = null)
    {
        $this->libChart = $libChart;
        return $this;
    }

    /**
     * Get lib
     *
     * @return ChartLib
     */
    public function getLibChart()
    {
        return $this->libChart;
    }


    /**
     * Set player
     *
     * @param PlayerChart $playerChart
     * @return $this
     */
    public function setPlayerChart(PlayerChart $playerChart = null)
    {
        $this->playerChart = $playerChart;
        return $this;
    }

    /**
     * Get playerChart
     *
     * @return PlayerChart
     */
    public function getPlayerChart()
    {
        return $this->playerChart;
    }


    /**
     * Get parseValue
     *
     * @return array
     */
    public function getParseValue()
    {
        $this->setParseValueFromValue();
        return $this->parseValue;
    }

    /**
     * Set parseValue
     *
     * @param $parseValue
     * @return $this
     */
    public function setParseValue($parseValue)
    {
        $this->parseValue = $parseValue;
        return $this;
    }


    /**
     *
     */
    public function setParseValueFromValue()
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
    public function setValueFromPaseValue()
    {
        if ($this->parseValue == null) {
            $this->value = null;
        } else {
            $this->value = (int)Score::formToBdd(
                $this->getLibChart()
                    ->getType()
                    ->getMask(),
                $this->parseValue
            );
        }
    }

    public function getFormatValue()
    {
        return Score::formatScore(
            $this->value,
            $this->getLibChart()
                ->getType()
                ->getMask()
        );
    }
}
