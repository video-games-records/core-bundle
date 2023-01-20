<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

trait AverageGameRankTrait
{
    /**
     * @ORM\Column(name="averageGameRank", type="float", nullable=true)
     */
    private ?float $averageGameRank;

    /**
     * @param float $averageGameRank
     * @return $this
     */
    public function setAverageGameRank(float $averageGameRank): static
    {
        $this->averageGameRank = $averageGameRank;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAverageGameRank(): ?float
    {
        return $this->averageGameRank;
    }
}
