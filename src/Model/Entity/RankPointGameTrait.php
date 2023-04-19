<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankPointGameTrait
{
    /**
     * @ORM\Column(name="rankPointGame", type="integer", nullable=false, options={"default" : 0})
     */
    private int $rankPointGame = 0;

    /**
     * @ORM\Column(name="pointGame", type="integer", nullable=false, options={"default" : 0})
     */
    private int $pointGame = 0;

    /**
     * @param int $rankPointGame
     * @return $this
     */
    public function setRankPointGame(int $rankPointGame): static
    {
        $this->rankPointGame = $rankPointGame;
        return $this;
    }

    /**
     * Get rankPointGame
     * @return int
     */
    public function getRankPointGame(): int
    {
        return $this->rankPointGame;
    }


    /**
     * @param int $pointGame
     * @return $this
     */
    public function setPointGame(int $pointGame): static
    {
        $this->pointGame = $pointGame;
        return $this;
    }

    /**
     * Get pointGame
     * @return integer
     */
    public function getPointGame(): int
    {
        return $this->pointGame;
    }
}
