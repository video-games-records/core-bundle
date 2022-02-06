<?php
namespace VideoGamesRecords\CoreBundle\Controller;

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
        $stats = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getProofStats();
        // Formatage
        $months = [];


        // MONTH
        foreach ($stats as $row) {
            $months[$row['month']][] = $row;
        }

        return $this->renderWithExtraParams(
            'VideoGamesRecordsCoreBundle:Admin:proof.stats.html.twig',
               [
                   'stats' => $months,
               ]);
    }
}
