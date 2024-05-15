<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Group;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\DataProvider\TopScoreProvider;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GetTopScore extends AbstractController
{
    private TopScoreProvider $topScoreProvider;

    public function __construct(TopScoreProvider $topScoreProvider)
    {
        $this->topScoreProvider = $topScoreProvider;
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return mixed
     * @throws ORMException
     */
    public function __invoke(Group $group, Request $request): mixed
    {
        return $this->topScoreProvider->load($group, $request->getLocale());
    }
}
