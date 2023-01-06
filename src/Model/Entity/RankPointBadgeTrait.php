<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RankPointBadgeTrait
{
    /**
     * @ORM\Column(name="rankBadge", type="integer", nullable=false)
     */
    private ?int $rankBadge;

    /**
     * @ORM\Column(name="pointBadge", type="integer", nullable=false)
     */
    private int $pointBadge = 0;

    /**
     * @param int $rankBadge
     * @return $this
     */
    public function setRankBadge(int $rankBadge): static
    {
        $this->rankBadge = $rankBadge;
        return $this;
    }

    /**
     * Get rankBadge
     * @return int|null
     */
    public function getRankBadge(): ?int
    {
        return $this->rankBadge;
    }

    /**
     * @param int $pointBadge
     * @return $this
     */
    public function setpointBadge(int $pointBadge): static
    {
        $this->pointBadge = $pointBadge;
        return $this;
    }

    /**
     * Get pointBadge
     * @return integer
     */
    public function getPointBadge(): int
    {
        return $this->pointBadge;
    }
}

