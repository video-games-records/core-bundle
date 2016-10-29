<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\UserChart;
use VideoGamesRecords\CoreBundle\Entity\UserChartLib;
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
        $charts = array();

        if ($data['type'] == 'chart') {
            $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($data['id']);
            $charts = array($chart);
        }

        $form = SubmitFormFactory::createSubmitForm(
            $this->get('form.factory')->create('Symfony\Component\Form\Extension\Core\Type\FormType', $data),
            $charts
        );

        $form->handleRequest($request);
        $data = $form->getData();


        if ($form->isSubmitted() && $form->isValid()) {
            $idUser = 1;

            //----- Init
            $nbInsert = 0;
            $nbUpdate = 0;

            foreach ($charts as $chart) {
                //----- init
                $isNull = false;
                $isModify = false;

                foreach ($chart->getLibs() as $lib) {
                    $oldValue = $data['user_' . $chart->getIdChart() . '_' . $lib->getIdLibChart()];
                    $newValue = '';
                    $values = array();

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
                $user = $em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $idUser);


                if (!$isNull && $isModify) {
                    $userChart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserChart')->find(
                        array(
                            'idUser' => $idUser,
                            'idChart' => $chart->getIdChart()
                        )
                    );

                    $isNew = false;
                    if ($userChart === null) {
                        $isNew = true;
                        $userChart = new UserChart();
                        $userChart->setUser($user);
                        $userChart->setChart($chart);
                    }

                    $userChart->setIdEtat(1);
                    //$userChart->setPeuveImage(0);
                    //$userChart->setIdVideo(0);
                    $userChart->setDateModif(new \DateTime());
                    $em->persist($userChart);
                    $em->flush();

                    foreach ($chart->getLibs() as $lib) {
                        $userChartLib = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserChartLib')->find(
                            array(
                                'idUser' => $idUser,
                                'idLibChart' => $lib->getIdLibChart()
                            )
                        );
                        if ($userChartLib === null) {
                            $userChartLib = new UserChartLib();
                            $userChartLib->setUser($user);
                            $userChartLib->setLibChart($lib);
                        }
                        $userChartLib->setValue($post[$lib->getIdLibChart()]);
                        $em->persist($userChartLib);
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
