<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankPointGameTrait
{
    /**
     * @ORM\Column(name="rankPointGame", type="integer", nullable=false)
     */
    private ?int $rankPointGame = 0;

    /**
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private int $pointGame = 0;

    /**
     * @param int $rankPointGame
     * @return void
     */
    public function setRankPointGame(int $rankPointGame): void
    {
        $this->rankPointGame = $rankPointGame;
    }

    /**
     * Get rankPointGame
     * @return integer
     */
    public function getRankPointGame(): int
    {
        return $this->rankPointGame;
    }


    /**
     * @param int $pointGame
     * @return void
     */
    public function setPointGame(int $pointGame): void
    {
        $this->pointGame = $pointGame;
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
