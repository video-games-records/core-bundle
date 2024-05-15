<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Repository\PlatformRepository;

class Autocomplete extends AbstractController
{
    private PlatformRepository $platformRepository;

    public function __construct(PlatformRepository $platformRepository)
    {
        $this->platformRepository = $platformRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function __invoke(Request $request): mixed
    {
        $q = $request->query->get('query', null);
        return $this->platformRepository->autocomplete($q);
    }
}
