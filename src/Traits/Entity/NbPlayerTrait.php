<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait NbPlayerTrait
{
    /**
     * @ORM\Column(name="nbPlayer", type="integer", nullable=false, options={"default":0})
     */
    private int $nbPlayer = 0;

    /**
     * Set nbPlayer
     *
     * @param integer $nbPlayer
     * @return $this
     */
    public function setNbPlayer(int $nbPlayer): static
    {
        $this->nbPlayer = $nbPlayer;

        return $this;
    }

    /**
     * Get nbPlayer
     *
     * @return integer
     */
    public function getNbPlayer(): int
    {
        return $this->nbPlayer;
    }
}
