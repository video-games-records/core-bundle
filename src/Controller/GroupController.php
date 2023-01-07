<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Service\Group\TopScoreProvider;

/**
 * Class GroupController
 */
class GroupController extends AbstractController
{
    private TopScoreProvider $topScoreProvider;

    public function __construct(TopScoreProvider $topScoreProvider)
    {
        $this->topScoreProvider = $topScoreProvider;
    }

    /**
     * @param Group   $group
     * @param Request $request
     * @return mixed
     * @throws ORMException
     */
    public function topScore(Group $group, Request $request)
    {
        return $this->topScoreProvider->load($group, $request->getLocale());
    }
}
