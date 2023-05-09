<?php

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;

class PlayerChartEvent extends Event
{
    protected PlayerChart $playerChart;
    protected ?int $oldRank;
    protected int $oldNbEqual;

    public function __construct(PlayerChart $playerChart, ?int $oldRank, int $oldNbEqual)
    {
        $this->playerChart = $playerChart;
        $this->oldRank = $oldRank;
        $this->oldNbEqual = $oldNbEqual;
    }

    public function getPlayerChart(): PlayerChart
    {
        return $this->playerChart;
    }

    public function getOldRank(): ?int
    {
        return $this->oldRank;
    }

    public function getOldNbEqual(): int
    {
        return $this->oldNbEqual;
    }
}
