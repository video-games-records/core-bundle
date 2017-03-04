<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;
use VideoGamesRecords\CoreBundle\Form\Type\SubmitFormFactory;
use VideoGamesRecords\CoreBundle\Tools\Score;

/**
 * Class SubmitController
 * @Route("/submit")
 */
class SubmitController extends Controller
{
    /**
     * @Route("/index", requirements={"id": "[1-9]\d*"}, name="vgr_submit_index")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $data = $request->request->get('form');
        /** @var \VideoGamesRecords\CoreBundle\Entity\Chart[] $charts */
        $charts = [];

        if ($data['type'] == 'chart') {
            $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($data['id']);
            $charts = [$chart];
        }

        $form = SubmitFormFactory::createSubmitForm(
            $this->get('form.factory')->create('Symfony\Component\Form\Extension\Core\Type\FormType', $data),
            $charts
        );

        $form->handleRequest($request);
        $data = $form->getData();


        if ($form->isSubmitted() && $form->isValid()) {
            $idPlayer = 1;

            //----- Init
            $nbInsert = 0;
            $nbUpdate = 0;

            foreach ($charts as $chart) {
                //----- init
                $isNull = false;
                $isModify = false;
                $post = [];

                foreach ($chart->getLibs() as $lib) {
                    $oldValue = $data['user_' . $chart->getIdChart() . '_' . $lib->getIdLibChart()];
                    $newValue = '';
                    $values = [];

                    $nbInput = $lib->getType()->getNbInput();
                    for ($i = 1; $i <= $nbInput; $i++) {
                        $value = $data['value_' . $chart->getIdChart() . '_' . $lib->getIdLibChart() . '_' . $i];
                        $newValue .= $value;
                        $values[] = $value;
                    }

                    //@TODO Validateur
                    if (($newValue === '') || !ctype_digit(trim($newValue, '-+'))) {
                        $isNull = true;
                        break;
                    }

                    $newValue = Score::formToBdd($lib->getType()->getMask(), $values);
                    if ($oldValue === null || ((int)$oldValue !== (int)$newValue) && ($newValue !== '')) {
                        $isModify = true;
                    }

                    $post[$lib->getIdLibChart()] = $newValue;
                }

                $em = $this->getDoctrine()->getManager();
                $player = $em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $idPlayer);


                if (!$isNull && $isModify) {
                    $playerChart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChart')->find(
                        [
                            'idUser' => $idPlayer,
                            'idChart' => $chart->getIdChart()
                        ]
                    );

                    $isNew = false;
                    if ($playerChart === null) {
                        $isNew = true;
                        $playerChart = new PlayerChart();
                        $playerChart->setPlayer($player);
                        $playerChart->setChart($chart);
                    }

                    $playerChart->setIdEtat(1);
                    //$userChart->setPeuveImage(0);
                    //$userChart->setIdVideo(0);
                    $playerChart->setDateModif(new \DateTime());
                    $em->persist($playerChart);
                    $em->flush();

                    foreach ($chart->getLibs() as $lib) {
                        $playerChartLib = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerChartLib')->find(
                            [
                                'idPlayer' => $idPlayer,
                                'idLibChart' => $lib->getIdLibChart()
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

                    $isNew ? $nbInsert++ : $nbUpdate++;
                }
            }

            //var_dump('nbInsert=' . $nbInsert);
            //var_dump('nbUpdate=' . $nbUpdate);
        }
    }
}
