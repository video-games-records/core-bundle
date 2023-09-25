<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait IsRankTrait
{
    /**
     * @ORM\Column(name="isRank", type="boolean", nullable=false, options={"default":true})
     */
    private bool $isRank = true;

    /**
     * Set isRank
     * @param bool $isRank
     * @return $this
     */
    public function setIsRank(bool $isRank): static
    {
        $this->isRank = $isRank;

        return $this;
    }

    /**
     * Get isRank
     * @return bool
     */
    public function getIsRank(): bool
    {
        return $this->isRank;
    }
}
