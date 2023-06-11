<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Repository\PlatformRepository;

/**
 * Class PlatformController
 */
class PlatformController extends AbstractController
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
    public function autocomplete(Request $request): mixed
    {
        $q = $request->query->get('query', null);
        return $this->platformRepository->autocomplete($q);
    }
}
