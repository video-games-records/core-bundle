<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GroupRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * @param Group $group
     * @param false $boolCopyLibChart
     */
    public function copy(Group $group, bool $boolCopyLibChart = false): void
    {
        $newGroup = new Group();
        $newGroup->setLibGroupEn($group->getLibGroupEn());
        $newGroup->setLibGroupFr($group->getLibGroupFr());
        $newGroup->setIsDlc($group->getIsDlc());
        $newGroup->setGame($group->getGame());

        /** @var Chart $chart */
        foreach ($group->getCharts() as $chart) {
            $newChart = new Chart();
            $newChart->setLibChartEn($chart->getLibChartEn());
            $newChart->setLibChartFr($chart->getLibChartFr());

            if ($boolCopyLibChart) {
                /** @var ChartLib $lib */
                foreach ($chart->getLibs() as $lib) {
                    $newLib = new ChartLib();
                    $newLib->setName($lib->getName());
                    $newLib->setType($lib->getType());
                    $newChart->addLib($newLib);
                }
            }
            $newGroup->addChart($newChart);
        }
        $this->getEntityManager()->persist($newGroup);
        $this->getEntityManager()->flush();
    }
}
