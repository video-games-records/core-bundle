<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Tools\Score;
use Symfony\Component\HttpFoundation\JsonResponse;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;
use Doctrine\DBAL\DBALException;

/**
 * Class PlayerChartController
 */
class PlayerChartController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function submit(Request $request)
    {

        /** @var \VideoGamesRecords\CoreBundle\Model\Api\PlayerChart $object */
        $object =  $this->get('serializer')->deserialize($request->getContent(), 'VideoGamesRecords\CoreBundle\Model\Api\PlayerChart', 'json');

        //@todo get idPlayer From Authentification
        $idPlayer = $object->getIdPlayer();
        $idChart = $object->getIdChart();
        $idPlaform = $object->getIdPlatform();
        $values = $object->getValues();

        $em = $this->getDoctrine()
            ->getManager();

        try {
            $player = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')->find($idPlayer);
            $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->find($idChart);
            $platform = null;
            if ($idPlaform != null) {
                $platform = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Platform')->find($idPlaform);
            }

            if ($player === null) {
                return new JsonResponse(
                    array(
                        'return' => 201,
                        'message' => 'Player not found'
                    )
                );
            }

            if ($chart === null) {
                return new JsonResponse(
                    array(
                        'return' => 202,
                        'message' => 'Chart not found'
                    )
                );
            }

            $post = array();
            foreach ($chart->getLibs() as $lib) {
                $idLibChart = $lib->getIdLibChart();
                if (!array_key_exists($idLibChart, $values)) {
                    return new JsonResponse(
                        array(
                            'return' => 203,
                            'message' => 'Wrong libChart'
                        )
                    );
                }
                $values = $values[$idLibChart];
                $value = implode('', $values);
                if (($value === '') || !ctype_digit(trim($value, '-+'))) {
                    return new JsonResponse(
                        array(
                            'return' => 204,
                            'message' => 'Value is Null'
                        )
                    );
                }
                $value = Score::formToBdd(
                    $lib->getType()
                        ->getMask(), $values
                );
                $post[$lib->getIdLibChart()] = $value;
            }

            $playerChart = $this->getDoctrine()
                ->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')
                ->getFromUnique(
                    $idPlayer, $idChart
                );

            $isNew = false;
            if ($playerChart === null) {
                $isNew = true;
                $playerChart = new PlayerChart();
                $playerChart->setPlayer($player);
                $playerChart->setChart($chart);
            }
            if ($platform != null) {
                $playerChart->setPlatform($platform);
            }

            $playerChart->setStatus($em->getReference('VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus', 1));
            $playerChart->setDateModif(new \DateTime());
            $em->persist($playerChart);

            foreach ($chart->getLibs() as $lib) {
                $playerChartLib = $this->getDoctrine()
                    ->getRepository('VideoGamesRecordsCoreBundle:PlayerChartLib')
                    ->find(
                        [
                            'player' => $idPlayer,
                            'libChart' => $lib->getIdLibChart()
                        ]
                    );
                if ($playerChartLib === null) {
                    $playerChartLib = new PlayerChartLib();
                    $playerChartLib->setPlayer($player);
                    $playerChartLib->setLibChart($lib);
                }
                $playerChartLib->setValue($post[$lib->getIdLibChart()]);
                $em->persist($playerChartLib);
            }
            $em->flush();

            return new JsonResponse(
                array(
                    'return' => 0,
                    'isNew' => $isNew,
                )
            );

        } catch (DBALException $e) {
            return new JsonResponse(
                array(
                    'return' => 101,
                    'message' => 'ERROR SQL'
                )
            );
        }


    }


    /*public function formAction($id)
    {
        $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($id);
        $charts = [$chart];

        $data = [
            'id' => $id,
            'type' => 'chart',
        ];

        $data = array_merge(
            $data,
            $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChartLib')->getFormValues($this->getPlayer(), $chart)
        );

        $form = SubmitFormFactory::createSubmitForm(
            $this->get('form.factory')->create('Symfony\Component\Form\Extension\Core\Type\FormType', $data),
            $charts
        );

        $breadcrumbs = $this->getChartBreadcrumbs($chart);
        $breadcrumbs->addItem($chart->getLibChart());

        return $this->render('VideoGamesRecordsCoreBundle:Submit:form.html.twig', ['chart' => $chart, 'charts' => $charts, 'form' => $form->createView()]);
    }*/
}
