<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait NbVideoTrait
{
    /**
     * @ORM\Column(name="nbVideo", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbVideo = 0;

    /**
     * Set nbVideo
     *
     * @param integer $nbVideo
     * @return $this
     */
    public function setNbVideo(int $nbVideo): static
    {
        $this->nbVideo = $nbVideo;

        return $this;
    }

    /**
     * Get nbVideo
     *
     * @return integer
     */
    public function getNbVideo(): int
    {
        return $this->nbGame;
    }
}
