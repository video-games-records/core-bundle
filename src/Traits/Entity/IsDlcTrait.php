<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait IsDlcTrait
{
    /**
     * @ORM\Column(name="isDlc", type="boolean", nullable=false, options={"default":false})
     */
    private bool $isDlc = true;

    /**
     * Set isDlc
     * @param bool $isDlc
     * @return $this
     */
    public function setIsDlc(bool $isDlc): static
    {
        $this->isDlc = $isDlc;

        return $this;
    }

    /**
     * Get isDlc
     * @return bool
     */
    public function getIsDlc(): bool
    {
        return $this->isDlc;
    }
}
