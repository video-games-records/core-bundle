<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Response;

class ProofAdminController extends CRUDController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return Response
     */
    public function statsAction(): Response
    {
        $stats = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->getProofStats();

        // Formatage
        $months = [];


        // MONTH
        foreach ($stats as $row) {
            $months[$row['month']][] = $row;
        }

        return $this->render(
            '@VideoGamesRecordsCore/Admin/Object/Proof/stats.html.twig',
            [
                'stats' => $months,
            ]
        );
    }
}
