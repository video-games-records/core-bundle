<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;

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
     * @return PlayerBadge[]|array
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
     * @throws Exception
     */
    public function majMasterBadge($game)
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->_em->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingPoints($game, 1);
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
                $playerBadge->setEndedAt(new DateTime());
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
     * @param $country
     * @throws Exception
     */
    public function majCountryBadge($country)
    {
        if ($country->getBadge() === null) {
            return;
        }

        //----- get ranking with maxRank = 1
        $ranking = $this->_em->getRepository('VideoGamesRecordsCoreBundle:Player')->getRankingCountry($country, 1);

        $players = array();
        foreach ($ranking as $player) {
            $players[$player->getId()] = 0;
        }

        $this->updateBadge($players, $country->getBadge());
    }

    /**
     * @param array $players
     * @param $badge
     * @throws Exception
     */
    private function updateBadge(array $players, $badge)
    {
        //----- get players with country badge
        $list = $this->getFromBadge($badge);

        //----- Remove country badge
        foreach ($list as $playerBadge) {
            $idPlayer = $playerBadge->getPlayer()->getId();
            //----- Remove badge
            if (!array_key_exists($idPlayer, $players)) {
                $playerBadge->setEndedAt(new DateTime());
                $this->_em->persist($playerBadge);
            }
            $players[$idPlayer] = 1;
        }
        //----- Add master badge
        foreach ($players as $idPlayer => $value) {
            if (0 === $value) {
                $playerBadge = new PlayerBadge();
                $playerBadge->setPlayer($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $idPlayer));
                $playerBadge->setBadge($badge);
                $this->_em->persist($playerBadge);
            }
        }
        $this->_em->flush();
    }

    /**
     * @param QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query)
    {
        $query->andWhere($query->expr()->isNull('pb.ended_at'));
    }

    /**
     * Maj user badges (Connexion / Forum)
     * @throws DBALException
     */
    public function majUserBadge()
    {
        $sql = "INSERT INTO vgr_player_badge (idPlayer, idBadge)
        SELECT vgr_player.id,badge.id
        FROM vgr_player,user,badge
        WHERE type = '%s'
        AND value <= user.%s
        AND vgr_player.normandie_user_id = user.id
        AND badge.id NOT IN (SELECT idBadge FROM vgr_player_badge WHERE idPlayer = vgr_player.id)";

        $this->_em->getConnection()->executeUpdate(sprintf($sql, 'Connexion', 'nbConnexion'));
        $this->_em->getConnection()->executeUpdate(sprintf($sql, 'Forum', 'nbForumMessage'));
    }

    /**
     * Maj player badges
     * @throws DBALException
     */
    public function majPlayerBadge()
    {
        $sql = " INSERT INTO vgr_player_badge (idPlayer, idBadge)
        SELECT vgr_player.id,badge.id
        FROM vgr_player,badge
        WHERE type = '%s'
        AND value <= vgr_player.%s
        AND badge.id NOT IN (SELECT idBadge FROM vgr_player_badge WHERE idPlayer = vgr_player.id)";

        $this->_em->getConnection()->executeUpdate(sprintf($sql, 'VgrChart', 'nbChart'));
        $this->_em->getConnection()->executeUpdate(sprintf($sql, 'VgrProof', 'nbChartProven'));
    }
}
