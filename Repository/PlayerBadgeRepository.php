<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Entity\Country;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;

class PlayerBadgeRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerBadge::class);
    }

    /**
     * @param $badge
     * @return PlayerBadge[]|array
     */
    public function getFromBadge($badge) : array
    {
        $query = $this->createQueryBuilder('pb');
        $query
            ->where('pb.badge = :badge')
            ->setParameter('badge', $badge);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }


    /**
     * @param Game $game
     * @throws Exception
     */
    public function majMasterBadge(Game $game)
    {
        //----- get ranking with maxRank = 1
        $ranking = $this->_em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerGame')->getRankingPoints($game, 1);
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
     * @param Country $country
     * @throws Exception
     */
    public function majCountryBadge(Country $country)
    {
        if ($country->getBadge() === null) {
            return;
        }

        //----- get ranking with maxRank = 1
        $ranking = $this->_em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->getRankingCountry($country, 1);

        $players = array();
        foreach ($ranking as $player) {
            $players[$player->getId()] = 0;
        }

        $this->updateBadge($players, $country->getBadge());
    }


    /**
     * @param Platform $platform
     * @throws Exception
     */
    public function majPlatformBadge(Platform $platform)
    {
        if ($platform->getBadge() === null) {
            return;
        }

        //----- get ranking with maxRank = 1
        $ranking = $this->_em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerPlatform')->getRankingPointPlatform($platform, 1);

        $players = array();
        foreach ($ranking as $playerPlatform) {
            $players[$playerPlatform->getPlayer()->getId()] = 0;
        }

        $this->updateBadge($players, $platform->getBadge());
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
        SELECT vgr_player.id,vgr_badge.id
        FROM vgr_player,user,vgr_badge
        WHERE type = '%s'
        AND value <= user.%s
        AND vgr_player.normandie_user_id = user.id
        AND vgr_badge.id NOT IN (SELECT idBadge FROM vgr_player_badge WHERE idPlayer = vgr_player.id)";

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
        SELECT vgr_player.id,vgr_badge.id
        FROM vgr_player,vgr_badge
        WHERE type = '%s'
        AND value <= vgr_player.%s
        AND vgr_badge.id NOT IN (SELECT idBadge FROM vgr_player_badge WHERE idPlayer = vgr_player.id)";

        $this->_em->getConnection()->executeUpdate(sprintf($sql, 'VgrChart', 'nbChart'));
        $this->_em->getConnection()->executeUpdate(sprintf($sql, 'VgrProof', 'nbChartProven'));
    }
}
