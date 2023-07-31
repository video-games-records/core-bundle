<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait LikeCountTrait
{
    /**
     * @ORM\Column(name="likeCount", type="integer", nullable=false, options={"default":0})
     */
    private int $likeCount = 0;

    /**
     * Set likeCount
     *
     * @param integer $likeCount
     * @return $this
     */
    public function setLikeCount(int $likeCount): static
    {
        $this->likeCount = $likeCount;

        return $this;
    }

    /**
     * Get likeCount
     *
     * @return integer
     */
    public function getLikeCount(): int
    {
        return $this->likeCount;
    }
}
