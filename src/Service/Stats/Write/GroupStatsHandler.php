<?php

namespace VideoGamesRecords\CoreBundle\Service\Stats\Write;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GroupStatsHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Group $group): void
    {
        $group->setNbChart(count($group->getCharts()));
        $nbPost = 0;
        foreach ($group->getCharts() as $chart) {
            $nbPost += $chart->getNbPost();
        }
        $group->setNbPost($nbPost);
        $this->em->flush();
    }
}
