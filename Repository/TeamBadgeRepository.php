<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\TeamBadge;

class TeamBadgeRepository extends EntityRepository
{

    /**
     * @param $idTeam
     * @param string $type
     * @return array
     */
    public function getFromTeam($idTeam, $type = 'master')
    {
        $query = $this->createQueryBuilder('tb');

        $query->join('tb.badge', 'b')
            ->addSelect('b');

        /*if ($type == 'master') {
            $query->join('b.games', 'g')
                ->addSelect('g');
        }*/

        if ($type == 'master') {
            $query->orderBy('tb.createdAt');
        } else {
            $query->orderBy('b.value', 'ASC');
        }

        $query->where('tb.idTeam = :idTeam')
            ->setParameter('idTeam', $idTeam)
            ->andWhere('b.type = :type')
            ->setParameter('type', $type);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }

    /**
     * @param $idBadge
     * @return array
     */
    public function getFromBadge($idBadge)
    {
        $query = $this->createQueryBuilder('tb');
        $query
            ->where('tb.idBadge = :idBadge')
            ->setParameter('idBadge', $idBadge);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }


    /**
     * @param $idGame
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function majMasterBadge($idGame)
    {
        $game = $this->_em->find('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);

        //----- get ranking with maxRank = 1
        $ranking = $this->_em->getRepository('VideoGamesRecordsCoreBundle:TeamGame')->getRankingPoints($idGame, 1);
        $teams = array();
        foreach ($ranking as $teamGame) {
            $teams[$teamGame->getIdTeam()] = 0;
        }

        //----- get teams with master badge
        $list = $this->getFromBadge($game->getIdBadge());

        //----- Remove master badge
        foreach ($list as $teamBadge) {
            $idTeam = $teamBadge->getIdTeam();
            //----- Remove badge
            if (!array_key_exists($idTeam, $teams)) {
                $teamBadge->setEndedAt(new \DateTime());
                $this->_em->persist($teamBadge);
            }
            $teams[$idTeam] = 1;
        }
        //----- Add master badge
        foreach ($teams as $idTeam => $value) {
            if ($value == 0) {
                $teamBadge = new TeamBadge();
                $teamBadge->setTeam($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam));
                $teamBadge->setBadge($this->_em->getReference('ProjetNormandie\BadgeBundle\Entity\Badge', $game->getIdBadge()));
                $this->_em->persist($teamBadge);
            }
        }
        $this->_em->flush();
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query)
    {
        $query->andWhere($query->expr()->isNull('tb.ended_at'));
    }
}
