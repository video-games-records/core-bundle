<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Read;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use VideoGamesRecords\CoreBundle\DataTransformer\TokenStorageToPlayerTransformer;
use VideoGamesRecords\CoreBundle\DataTransformer\TokenStorageToTeamTransformer;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;

class DefaultRankingQuery
{
    protected EntityManagerInterface $em;
    protected TokenStorageToPlayerTransformer $tokenStorageToPlayerTransformer;
    protected TokenStorageToTeamTransformer $tokenStorageToTeamTransformer;
    private TokenStorageInterface $tokenStorage;


    public function __construct(
        EntityManagerInterface $em,
        TokenStorageToPlayerTransformer $tokenStorageToPlayerTransformer,
        TokenStorageToTeamTransformer $tokenStorageToTeamTransformer,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $em;
        $this->tokenStorageToPlayerTransformer = $tokenStorageToPlayerTransformer;
        $this->tokenStorageToTeamTransformer = $tokenStorageToTeamTransformer;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @throws ORMException
     */
    protected function getPlayer(): ?Player
    {
        return $this->tokenStorageToPlayerTransformer->transform($this->tokenStorage->getToken());
    }

    /**
     * @throws ORMException
     */
    protected function getTeam(): ?Team
    {
        return $this->tokenStorageToTeamTransformer->transform($this->tokenStorage->getToken());
    }
}
