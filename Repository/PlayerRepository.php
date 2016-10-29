<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Entity\Player;

/**
 * PlayerRepository
 */
class PlayerRepository extends EntityRepository
{
    /**
     * @param \AppBundle\Entity\User $user
     * @return \VideoGamesRecords\CoreBundle\Entity\Player
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPlayerFromUser($user)
    {
        $qb = $this->createQueryBuilder('player')
            ->where('player.normandieUser = :userId')
            ->setParameter('userId', $user->getId());

        $player = $qb->getQuery()->getOneOrNullResult();

        return (null !== $player) ? $player : $this->createPlayerFromUser($user);
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @return \VideoGamesRecords\CoreBundle\Entity\Player
     */
    private function createPlayerFromUser($user)
    {
        $player = new Player();
        $player->setNormandieUser($user);

        $this->getEntityManager()->persist($player);
        $this->getEntityManager()->flush();

        return $player;
    }
}
