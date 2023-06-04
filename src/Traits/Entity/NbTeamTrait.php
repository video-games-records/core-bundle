<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait NbTeamTrait
{
    /**
     * @ORM\Column(name="nbTeam", type="integer", nullable=false, options={"default":0})
     */
    private int $nbTeam = 0;

    /**
     * Set nbTeam
     *
     * @param integer $nbTeam
     * @return $this
     */
    public function setNbTeam(int $nbTeam): static
    {
        $this->nbTeam = $nbTeam;

        return $this;
    }

    /**
     * Get nbTeam
     *
     * @return integer
     */
    public function getNbTeam(): int
    {
        return $this->nbTeam;
    }
}
