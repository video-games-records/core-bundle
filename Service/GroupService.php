<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Repository\GroupRepository;
use VideoGamesRecords\CoreBundle\Repository\PlayerGroupRepository;
use VideoGamesRecords\CoreBundle\Repository\TeamGroupRepository;

class GroupService
{
    private GroupRepository $groupRepository;
    private PlayerGroupRepository $playerGroupRepository;
    private TeamGroupRepository $teamGameRepository;

    public function __construct(
        GroupRepository $groupRepository,
        PlayerGroupRepository $playerGroupRepository,
        TeamGroupRepository $teamGroupRepository
    )
    {
        $this->groupRepository = $groupRepository;
        $this->playerGroupRepository = $playerGroupRepository;
        $this->teamGameRepository = $teamGroupRepository;
    }

    /**
     * @param int $idGroup
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ExceptionInterface
     */
    public function majPlayerGroup(int $idGroup)
    {
        $group = $this->getGroup($idGroup);
        if ($group) {
            $this->playerGroupRepository->maj($group);
        }
    }

    /**
     * @param int $idGroup
     * @throws ExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function majTeamGroup(int $idGroup)
    {
        $group = $this->getGroup($idGroup);
        if ($group) {
            $this->teamGameRepository->maj($group);
        }
    }

    /**
     * @param $idGroup
     * @return Group|null
     */
    private function getGroup($idGroup) : ?Group
    {
        return $this->groupRepository->find($idGroup);
    }
}
