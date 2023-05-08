<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

trait NbChartProvenTrait
{
    /**
     * @ORM\Column(name="nbChartProven", type="integer", nullable=false, options={"default":0})
     */
    private int $nbChartProven = 0;

    /**
     * Set nbChartProven
     * @param integer $nbChartProven
     * @return $this
     */
    public function setNbChartProven(int $nbChartProven): static
    {
        $this->nbChartProven = $nbChartProven;
        return $this;
    }

    /**
     * Get nbChartProven
     *
     * @return integer
     */
    public function getNbChartProven(): int
    {
        return $this->nbChartProven;
    }
}
