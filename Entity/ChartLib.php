<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_chartlib", indexes={@ORM\Index(name="idLibChart", columns={"idLibChart"}), @ORM\Index(name="idxIdChart", columns={"idChart"}), @ORM\Index(name="idxIdType", columns={"idType"}) })
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartLibRepository")
 */
class ChartLib
{

    /**
     * @var integer
     *
     * @ORM\Column(name="idLibChart", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idLibChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="idChart", type="integer")
     */
    private $idChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdType", type="integer")
     */
    private $idType;

    /**
     * @var string
     *
     * @ORM\Column(name="lib", type="string", length=100, nullable=true)
     */
    private $lib;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $dateCreation;

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
     * @var Type
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
     * @param string $lib
     * @return ChartLib
     */
    public function setLib($lib)
    {
        $this->lib = $lib;
        return $this;
    }

    /**
     * Get lib
     *
     * @return string
     */
    public function getLib()
    {
        return $this->lib;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return ChartLib
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
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
     * Get idChart
     *
     * @return integer
     */
    public function getIdChart()
    {
        return $this->idChart;
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
