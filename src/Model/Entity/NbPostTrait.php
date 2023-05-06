<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

trait NbPostTrait
{
    /**
     * @ORM\Column(name="nbPost", type="integer", nullable=false, options={"default":0})
     */
    private int $nbPost = 0;

    /**
     * Set nbPost
     *
     * @param integer $nbPost
     * @return $this
     */
    public function setNbPost(int $nbPost): static
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    /**
     * Get nbPost
     *
     * @return integer
     */
    public function getNbPost(): int
    {
        return $this->nbPost;
    }
}
