<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait IsActiveTrait
{
    /**
     * @ORM\Column(name="isActive", type="boolean", nullable=false, options={"default":true})
     */
    private bool $isActive = true;

    /**
     * Set isActive
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }
}
