<?php

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Proof;

class ProofEvent extends Event
{
    protected Proof $proof;

    public function __construct(Proof $proof)
    {
        $this->proof = $proof;
    }

    public function getProof(): Proof
    {
        return $this->proof;
    }
}