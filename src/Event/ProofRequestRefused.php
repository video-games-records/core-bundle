<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use VideoGamesRecords\CoreBundle\Entity\ProofRequest;

class ProofRequestRefused extends Event
{
    protected ProofRequest $proofRequest;

    public function __construct(ProofRequest $proofRequest)
    {
        $this->proofRequest = $proofRequest;
    }

    public function getProofRequest(): ProofRequest
    {
        return $this->proofRequest;
    }
}
