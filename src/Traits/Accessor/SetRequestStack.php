<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Accessor;

use Symfony\Component\HttpFoundation\RequestStack;

trait SetRequestStack
{
    private RequestStack $requestStack;

    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }
}
