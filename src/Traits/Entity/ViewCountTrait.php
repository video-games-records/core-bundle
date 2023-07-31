<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait ViewCountTrait
{
    /**
     * @ORM\Column(name="viewCount", type="integer", nullable=false, options={"default":0})
     */
    private int $viewCount = 0;

    /**
     * Set viewCount
     *
     * @param integer $viewCount
     * @return $this
     */
    public function setViewCount(int $viewCount): static
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * Get viewCount
     *
     * @return integer
     */
    public function getViewCount(): int
    {
        return $this->viewCount;
    }
}
