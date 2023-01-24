<?php
namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProofAdminController
 */
class ProofAdminController extends CRUDController
{

    /**
     * @return Response
     */
    public function statsAction(): Response
    {
        $stats = $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->getProofStats();
        // Formatage
        $months = [];


        // MONTH
        foreach ($stats as $row) {
            $months[$row['month']][] = $row;
        }

        return $this->renderWithExtraParams(
            '@VideoGamesRecordsCore/Admin/proof.stats.html.twig',
            [
                'stats' => $months,
            ]
        );
    }
}
