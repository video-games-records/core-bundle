<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait NbChartProvenWithoutDlcTrait
{
    /**
     * @ORM\Column(name="nbChartProvenWithoutDlc", type="integer", nullable=false, options={"default":0})
     */
    private int $nbChartProvenWithoutDlc = 0;

    /**
     * Set nbChartProvenWithoutDlc
     * @param integer $nbChartProvenWithoutDlc
     * @return $this
     */
    public function setNbChartProvenWithoutDlc(int $nbChartProvenWithoutDlc): static
    {
        $this->nbChartProvenWithoutDlc = $nbChartProvenWithoutDlc;
        return $this;
    }

    /**
     * Get nbChartProvenWithoutDlc
     *
     * @return integer
     */
    public function getNbChartProvenWithoutDlc(): int
    {
        return $this->nbChartProvenWithoutDlc;
    }
}
