<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class TeamSerieUpdated extends Event
{
    protected Serie $serie;

    public function __construct(Serie $serie)
    {
        $this->serie = $serie;
    }

    public function getSerie(): Serie
    {
        return $this->serie;
    }
}
