<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

trait NbChartTrait
{
    /**
     * @ORM\Column(name="nbChart", type="integer", nullable=false, options={"default":0})
     */
    private int $nbChart = 0;

    /**
     * Set nbChart
     *
     * @param integer $nbChart
     * @return $this
     */
    public function setNbChart(int $nbChart): static
    {
        $this->nbChart = $nbChart;

        return $this;
    }

    /**
     * Get nbChart
     *
     * @return integer
     */
    public function getNbChart(): int
    {
        return $this->nbChart;
    }
}
