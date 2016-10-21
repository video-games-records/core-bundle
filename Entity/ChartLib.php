<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_chartlib", indexes={@ORM\Index(name="idLibChart", columns={"idLibChart"}), @ORM\Index(name="idxIdChart", columns={"idChart"}), @ORM\Index(name="idxIdType", columns={"idType"}) })
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartLibRepository")
 */
class ChartLib
{
    use Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="idLibChart", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idLibChart;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", inversedBy="libs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="idChart")
     * })
     */
    private $chart;

    /**
     * @var ChartType
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idType", referencedColumnName="idType")
     * })
     */
    private $type;

    /**
     * Set lib
     *
     * @param string $name
     * @return ChartLib
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get lib
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get idLibChart
     *
     * @return integer
     */
    public function getIdLibChart()
    {
        return $this->idLibChart;
    }

    /**
     * Set idLibChart
     *
     * @param int $idLibChart
     * @return \VideoGamesRecords\CoreBundle\Entity\ChartLib
     */
    public function setIdLibChart($idLibChart)
    {
        $this->idLibChart = $idLibChart;

        return $this;
    }

    /**
     * Set chart
     *
     * @param Chart $chart
     * @return ChartLib
     */
    public function setChart(Chart $chart = null)
    {
        $this->chart = $chart;
        return $this;
    }

    /**
     * Get chart
     *
     * @return Chart
     */
    public function getChart()
    {
        return $this->chart;
    }

    /**
     * Set type
     *
     * @param ChartType $type
     * @return ChartType
     */
    public function setType(ChartType $type = null)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return ChartType
     */
    public function getType()
    {
        return $this->type;
    }
}
