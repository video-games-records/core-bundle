<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

/**
 * TeamGameRepository
 */
class TeamGameRepository extends EntityRepository
{

    /**
     * @param $idGame
     * @return array
     */
    public function maj($idGame)
    {
        //----- delete
        $query = $this->_em->createQuery(
            'DELETE VideoGamesRecords\CoreBundle\Entity\TeamGame tg WHERE tg.idGame = :idGame'
        );
        $query->setParameter('idGame', $idGame);
        $query->execute();

        //----- select ans save result in array
        $query = $this->_em->createQuery("
            SELECT
                tg.idTeam,
                (g.idGame) as idGame,
                '' as rankPoint,
                '' as rankMedal,
                SUM(tg.rank0) as rank0,
                SUM(tg.rank1) as rank1,
                SUM(tg.rank2) as rank2,
                SUM(tg.rank3) as rank3,
                SUM(tg.pointChart) as pointChart
            FROM VideoGamesRecords\CoreBundle\Entity\TeamGroup tg
            JOIN tg.group g
            WHERE g.idGame = :idGame
            GROUP BY tg.idTeam
            ORDER BY pointChart DESC");


        $query->setParameter('idGame', $idGame);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPoint', ['pointChart'], true);
        $list = Ranking::order($list, ['rank0' => 'DESC', 'rank1' => 'DESC', 'rank2' => 'DESC', 'rank3' => 'DESC']);
        $list = Ranking::addRank($list, 'rankMedal', ['rank0', 'rank1', 'rank2', 'rank3']);
        $list = Ranking::calculateGamePoints($list, array('rankPoint', 'nbEqual'), 'pointGame', 'pointChart');

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        foreach ($list as $row) {
            $teamGame = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\TeamGame'
            );
            $teamGame->setTeam($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $row['idTeam']));
            $teamGame->setGame($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Game', $idGame));

            $this->_em->persist($teamGame);
        }
        $this->_em->flush();
    }
}
