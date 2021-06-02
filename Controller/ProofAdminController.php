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
    public function statsAction()
    {
        $stats = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->getProofStats();
        // Formatage
        $months = [];


        // MONTH
        foreach ($stats as $row) {
            $months[$row['month']][] = $row;
        }

        /*$table = array();
        $index = 0;
        $k = 0;
        foreach ($months as $month => $data) {
            if ($k == 4) {
                $k = 0;
                $index++;
            }
            $table[$index][$month] = $data;
            $k++;
        }
        /*echo '<pre>';
        var_dump($table); exit;*/


        return $this->renderWithExtraParams(
            'VideoGamesRecordsCoreBundle:Admin:proof.stats.html.twig',
               [
                   'stats' => $months,
               ]);
    }
}
