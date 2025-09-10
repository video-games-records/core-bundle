<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Country;

class PlayerCountryUpdated extends Event
{
    protected Country $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }
}
