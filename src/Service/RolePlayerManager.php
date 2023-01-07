<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

class RolePlayerManager
{
    private EntityManagerInterface $em;

    private const GROUP_PLAYER_ID = 2;
    private const GROUP_PLAYER_DISABLED_AUTO_ID = 9;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     *@throws ORMException
     */
    public function majRulesOfThree(): void
    {
        $group1 = $this->em->getReference('ProjetNormandie\UserBundle\Entity\Group', self::GROUP_PLAYER_ID);
        $group2 = $this->em->getReference('ProjetNormandie\UserBundle\Entity\Group', self::GROUP_PLAYER_DISABLED_AUTO_ID);

        $players = $this->getPlayerToDisabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->removeGroup($group1);
            $user->addGroup($group2);
        }
        $this->em->flush();

        $players = $this->getPlayerToEnabled();
        foreach ($players as $player) {
            $user = $player->getUser();
            $user->addGroup($group1);
            $user->removeGroup($group2);
        }
        $this->em->flush();
    }


    /**
     * Get list who cant send scores
     */
    private function getPlayerToDisabled()
    {
        $query = $this->em->createQueryBuilder()
            ->select('p')
            ->from('VideoGamesRecords\CoreBundle\Entity\Player', 'p')
            ->where('(p.nbChartDisabled >= :nbChartDisabled OR (p.nbChart > :nbChart AND p.nbChart/p.nbChartProven * 300 < :percentage))')
            ->setParameter('nbChartDisabled', 30)
            ->setParameter('nbChart', 300)
            ->setParameter('percentage', 3)
            ->andWhere('p.user IN (SELECT u FROM ProjetNormandie\UserBundle\Entity\User u join u.groups g WHERE g.id = :idGroup)')
            ->setParameter('idGroup', self::GROUP_PLAYER_ID);
        return $query->getQuery()->getResult();
    }

    /**
     * Get list that can now send scores
     */
    private function getPlayerToEnabled()
    {
        $query = $this->em->createQueryBuilder()
            ->select('p')
            ->from('VideoGamesRecords\CoreBundle\Entity\Player', 'p')
            ->where('(p.nbChartDisabled < :nbChartDisabled AND (p.nbChart > :nbChart AND p.nbChart/p.nbChartProven * 300 >= :percentage))')
            ->setParameter('nbChartDisabled', 30)
            ->setParameter('nbChart', 300)
            ->setParameter('percentage', 3)
            ->andWhere('p.user IN (SELECT u FROM ProjetNormandie\UserBundle\Entity\User u join u.groups g WHERE g.id = :idGroup)')
            ->setParameter('idGroup', self::GROUP_PLAYER_DISABLED_AUTO_ID);
        return $query->getQuery()->getResult();
    }
}
