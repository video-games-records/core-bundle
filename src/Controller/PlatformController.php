<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use VideoGamesRecords\CoreBundle\Entity\Game;
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
