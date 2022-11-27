<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankCupTrait
{
    /**
     * @ORM\Column(name="rankCup", type="integer", nullable=false)
     */
    private int $rankCup = 0;

    /**
     * @ORM\Column(name="gameRank0", type="integer", nullable=false)
     */
    private int $gameRank0 = 0;

    /**
     * @ORM\Column(name="gameRank1", type="integer", nullable=false)
     */
    private int $gameRank1 = 0;

    /**
     * @ORM\Column(name="gameRank2", type="integer", nullable=false)
     */
    private int $gameRank2 = 0;

    /**
     * @ORM\Column(name="gameRank3", type="integer", nullable=false)
     */
    private int $gameRank3 = 0;

    /**
     * @param int $rankCup
     * @return void
     */
    public function setRankCup(int $rankCup): void
    {
        $this->rankCup = $rankCup;
    }

    /**
     * Get rankCup
     * @return integer
     */
    public function getRankCup(): int
    {
        return $this->rankCup;
    }

    /**
     * @param int $gameRank0
     * @return void
     */
    public function setGameRank0(int $gameRank0): void
    {
        $this->gameRank0 = $gameRank0;
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
     * @return void
     */
    public function setGameRank1(int $gameRank1): void
    {
        $this->gameRank1 = $gameRank1;
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
     * @return void
     */
    public function setGameRank2(int $gameRank2): void
    {
        $this->gameRank2 = $gameRank2;
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
     * @return void
     */
    public function setGameRank3(int $gameRank3): void
    {
        $this->gameRank3 = $gameRank3;
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

