<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ChartController
 */
class ChartController extends Controller
{


    /**
     * @param Chart    $chart
     * @param Request $request
     * @return mixed
     */
    public function playerRanking(Chart $chart, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 20);
        $idPlayer = $request->query->get('idPlayer', null);
        $ranking = $this->getDoctrine()
            ->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')
            ->getRanking($chart, null, $maxRank);

        return $ranking;
    }
}
