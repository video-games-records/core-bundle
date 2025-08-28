<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Event\Admin;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;

class AdminPlayerChartUpdated extends Event
{
    protected PlayerChart $playerChart;

    public function __construct(PlayerChart $playerChart)
    {
        $this->playerChart = $playerChart;
    }

    public function getPlayerChart(): PlayerChart
    {
        return $this->playerChart;
    }
}