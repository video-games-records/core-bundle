<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait NbEqualTrait
{
    /**
     * @ORM\Column(name="nbEqual", type="integer", nullable=false, options={"default" : 1})
     */
    private int $nbEqual = 1;

    /**
     * Set nbEqual
     * @param integer $nbEqual
     * @return $this
     */
    public function setNbEqual(int $nbEqual): static
    {
        $this->nbEqual = $nbEqual;
        return $this;
    }

    /**
     * Get nbEqual
     *
     * @return integer
     */
    public function getNbEqual(): int
    {
        return $this->nbEqual;
    }
}
