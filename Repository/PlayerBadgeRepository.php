<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;
use VideoGamesRecords\CoreBundle\Entity\Game;

class PlayerBadgeRepository extends EntityRepository
{
    /**
     * @param $idPlayer
     * @param string $type
     * @return array
     */
    public function getFromPlayer($idPlayer, $type = 'master')
    {
        $query = $this->createQueryBuilder('pb');

        $query->join('pb.badge', 'b')
            ->addSelect('b');

        /*if ($type == 'master') {
            $query->join('b.games', 'g')
                ->addSelect('g');
        }*/

        if ($type === 'master') {
            $query->orderBy('pb.createdAt');
        } else {
            $query->orderBy('b.value', 'ASC');
        }

        $query->where('pb.idPlayer = :idPlayer')
            ->setParameter('idPlayer', $idPlayer)
            ->andWhere('b.type = :type')
            ->setParameter('type', $type);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }

    /**
     * @param $badge
     * @return \VideoGamesRecords\CoreBundle\Entity\PlayerBadge[]|array
     */
    public function getFromBadge($badge)
    {
        $query = $this->createQueryBuilder('pb');
        $query
            ->where('pb.badge = :badge')
            ->setParameter('badge', $badge);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }


    /**
     * @param $game
     * @throws \Exception
     */
    public function majMasterBadge($game)
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->_em->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingPoints($game->getId(), 1);
        $players = array();
        foreach ($ranking as $playerGame) {
            $players[$playerGame->getPlayer()->getId()] = 0;
        }

        //----- get players with master badge
        $list = $this->getFromBadge($game->getBadge());

        //----- Remove master badge
        foreach ($list as $playerBadge) {
            $idPlayer = $playerBadge->getPlayer()->getId();
            //----- Remove badge
            if (!array_key_exists($idPlayer, $players)) {
                $playerBadge->setEndedAt(new \DateTime());
                $this->_em->persist($playerBadge);
            }
            $players[$idPlayer] = 1;
        }
        //----- Add master badge
        foreach ($players as $idPlayer => $value) {
            if (0 === $value) {
                $playerBadge = new PlayerBadge();
                $playerBadge->setPlayer($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $idPlayer));
                $playerBadge->setBadge($game->getBadge());
                $this->_em->persist($playerBadge);
            }
        }
        $this->_em->flush();
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query)
    {
        $query->andWhere($query->expr()->isNull('pb.ended_at'));
    }
}
