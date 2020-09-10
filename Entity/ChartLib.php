<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_chartlib", indexes={@ORM\Index(name="idLibChart", columns={"idLibChart"}), @ORM\Index(name="idxIdChart", columns={"idChart"}), @ORM\Index(name="idxIdType", columns={"idType"}) })
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartLibRepository")
 */
class ChartLib implements TimestampableInterface
{
    use TimestampableTrait;

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
     * @Assert\Length(max="100")
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", inversedBy="libs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id")
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
     * @return $this
     */
    public function setName(string $name)
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
     * @return $this
     */
    public function setIdLibChart(int $idLibChart)
    {
        $this->idLibChart = $idLibChart;

        return $this;
    }

    /**
     * Set chart
     * @param Chart|null $chart
     * @return $this
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
     * @param ChartType|object|null $type
     * @return $this
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
