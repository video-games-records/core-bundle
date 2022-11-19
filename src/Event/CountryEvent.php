<?php

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Country;

class CountryEvent extends Event
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