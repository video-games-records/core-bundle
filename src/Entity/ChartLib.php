<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_chartlib")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartLibRepository")
 */
class ChartLib implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Column(name="idLibChart", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $idLibChart = null;

    /**
     * @Assert\Length(max="100")
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private ?string $name;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", inversedBy="libs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id")
     * })
     */
    private Chart $chart;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartType", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idType", referencedColumnName="idType")
     * })
     */
    private ChartType $type;

    /**
     * Set lib
     *
     * @param string|null $name
     * @return $this
     */
    public function setName(string $name = null): ChartLib
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get lib
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get idLibChart
     *
     * @return integer
     */
    public function getIdLibChart(): ?int
    {
        return $this->idLibChart;
    }

    /**
     * Set idLibChart
     *
     * @param int $idLibChart
     * @return $this
     */
    public function setIdLibChart(int $idLibChart): ChartLib
    {
        $this->idLibChart = $idLibChart;

        return $this;
    }

    /**
     * Set chart
     * @param Chart $chart
     * @return $this
     */
    public function setChart(Chart $chart): ChartLib
    {
        $this->chart = $chart;
        return $this;
    }

    /**
     * Get chart
     *
     * @return Chart
     */
    public function getChart(): Chart
    {
        return $this->chart;
    }

    /**
     * Set type
     * @param ChartType $type
     * @return $this
     */
    public function setType(ChartType $type): ChartLib
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return ChartType
     */
    public function getType(): ChartType
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getType()->getName(), $this->idLibChart);
    }
}
