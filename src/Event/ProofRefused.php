<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\Proof;

class ProofRefused extends Event
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
