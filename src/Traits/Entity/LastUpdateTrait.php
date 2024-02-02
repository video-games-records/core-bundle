<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use DateTime;

trait LastUpdateTrait
{
    /**
     * @ORM\Column(name="lastUpdate", type="datetime", nullable=true)
     */
    private DateTime $lastUpdate;


    /**
     * Set lastUpdate
     *
     * @param DateTime $lastUpdate
     * @return $this
     */
    public function setLastUpdate(DateTime $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return DateTime
     */
    public function getLastUpdate(): DateTime
    {
        return $this->lastUpdate;
    }
}
