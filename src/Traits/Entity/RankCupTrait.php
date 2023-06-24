<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankCupTrait
{
    /**
     * @ORM\Column(name="rankCup", type="integer", nullable=false, options={"default" : 0})
     */
    private int $rankCup = 0;

    /**
     * @ORM\Column(name="gameRank0", type="integer", nullable=false, options={"default" : 0})
     */
    private int $gameRank0 = 0;

    /**
     * @ORM\Column(name="gameRank1", type="integer", nullable=false, options={"default" : 0})
     */
    private int $gameRank1 = 0;

    /**
     * @ORM\Column(name="gameRank2", type="integer", nullable=false, options={"default" : 0})
     */
    private int $gameRank2 = 0;

    /**
     * @ORM\Column(name="gameRank3", type="integer", nullable=false, options={"default" : 0})
     */
    private int $gameRank3 = 0;

    /**
     * @param int $rankCup
     * @return $this
     */
    public function setRankCup(int $rankCup): static
    {
        $this->rankCup = $rankCup;
        return $this;
    }

    /**
     * Get rankCup
     * @return int
     */
    public function getRankCup(): int
    {
        return $this->rankCup;
    }

    /**
     * @param int $gameRank0
     * @return $this
     */
    public function setGameRank0(int $gameRank0): static
    {
        $this->gameRank0 = $gameRank0;
        return $this;
    }

    /**
     * Get gameRank0
     * @return integer
     */
    public function getGameRank0(): int
    {
        return $this->gameRank0;
    }

    /**
     * @param int $gameRank1
     * @return $this
     */
    public function setGameRank1(int $gameRank1): static
    {
        $this->gameRank1 = $gameRank1;
        return $this;
    }

    /**
     * Get gameRank1
     * @return integer
     */
    public function getGameRank1(): int
    {
        return $this->gameRank1;
    }

    /**
     * @param int $gameRank2
     * @return $this
     */
    public function setGameRank2(int $gameRank2): static
    {
        $this->gameRank2 = $gameRank2;
        return $this;
    }

    /**
     * Get gameRank2
     * @return integer
     */
    public function getGameRank2(): int
    {
        return $this->gameRank2;
    }

    /**
     * @param int $gameRank3
     * @return $this
     */
    public function setGameRank3(int $gameRank3): static
    {
        $this->gameRank3 = $gameRank3;
        return $this;
    }

    /**
     * Get gameRank3
     * @return integer
     */
    public function getGameRank3(): int
    {
        return $this->gameRank3;
    }
}
