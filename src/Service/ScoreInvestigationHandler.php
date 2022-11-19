<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;

class ScoreInvestigationHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws ORMException
     */
    public function process(): void
    {
        $list = $this->getScoreToDesactivate();
        $statut = $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus', PlayerChartStatus::ID_STATUS_NOT_PROOVED);
        /** @var PlayerChart $playerChart */
        foreach ($list as $playerChart) {
            $playerChart->setStatus($statut);
        }
        $this->em->flush();
    }

     /**
     * @return array
     */
    private function getScoreToDesactivate(): array
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P14D'));

        $query = $this->em->createQueryBuilder()
            ->select('pc')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->where('pc.status = :idStatus')
            ->setParameter('idStatus', PlayerChartStatus::ID_STATUS_INVESTIGATION)
            ->andWhere('pc.dateInvestigation < :date')
            ->setParameter('date', $date->format('Y-m-d'));
        return $query->getQuery()->getResult();
    }
}
