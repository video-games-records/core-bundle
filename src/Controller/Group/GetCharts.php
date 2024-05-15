<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Group;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\ValueObject\GroupOrderBy;

class GetCharts extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return array
     */
    public function __invoke(Group $group, Request $request): array
    {
        $orderBy = ['id' => 'ASC'];
        if ($group->getOrderBy() === GroupOrderBy::NAME) {
            $locale = Locale::getDefault();
            if ($locale === 'fr') {
                $orderBy = ['libChartFr' => 'ASC'];
            } else {
                $orderBy = ['libChartEn' => 'ASC'];
            }
        } elseif ($group->getOrderBy() === GroupOrderBy::ID) {
            $orderBy = ['id' => 'ASC'];
        }
        return $this->em->getRepository(Chart::class)->findBy(
            ['group' => $group],
            $orderBy
        );
    }
}
