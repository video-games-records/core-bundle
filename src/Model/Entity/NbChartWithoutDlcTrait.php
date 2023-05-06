<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

trait NbChartWithoutDlcTrait
{
    /**
     * @ORM\Column(name="nbChartWithoutDlc", type="integer", nullable=false, options={"default":0})
     */
    private int $nbChartWithoutDlc = 0;

    /**
     * Set nbChartWithoutDlc
     * @param integer $nbChartWithoutDlc
     * @return $this
     */
    public function setNbChartWithoutDlc(int $nbChartWithoutDlc): static
    {
        $this->nbChartWithoutDlc = $nbChartWithoutDlc;
        return $this;
    }

    /**
     * Get nbChartWithoutDlc
     *
     * @return integer
     */
    public function getNbChartWithoutDlc(): int
    {
        return $this->nbChartWithoutDlc;
    }
}
