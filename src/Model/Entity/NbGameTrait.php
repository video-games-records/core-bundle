<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

trait NbGameTrait
{
    /**
     * @ORM\Column(name="nbGame", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbGame = 0;

    /**
     * Set nbGame
     *
     * @param integer $nbGame
     * @return $this
     */
    public function setNbGame(int $nbGame): static
    {
        $this->nbGame = $nbGame;

        return $this;
    }

    /**
     * Get nbGame
     *
     * @return integer
     */
    public function getNbGame(): int
    {
        return $this->nbGame;
    }
}
