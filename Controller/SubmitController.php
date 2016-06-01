<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use VideoGamesRecords\CoreBundle\Entity\UserChart;
use VideoGamesRecords\CoreBundle\Entity\UserChartLib;

/**
 * Class SubmitController
 * @Route("/submit")
 */
class SubmitController extends Controller
{


    /**
     * @Route("/index", requirements={"id": "[1-9]\d*"}, name="vgr_submit_index")
     * @Method({"GET", "POST"})
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $data = $request->request->get('form');

        if ($data['type'] == 'chart') {
            $chart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($data['id']);
            $charts = array($chart);
        } else {

        }


        $form = \VideoGamesRecords\CoreBundle\Form\Type\SubmitFormFactory::createSubmitForm(
            $this->get('form.factory')->create('Symfony\Component\Form\Extension\Core\Type\FormType', $data),
            $charts
        );

        $form->handleRequest($request);
        $data = $form->getData();


        if ($form->isSubmitted() && $form->isValid()) {
            $idMembre = 1;

            //----- Init
            $nbInsert = 0;
            $nbUpdate = 0;

            foreach ($charts as $chart) {

                //----- init
                $isNull = false;
                $isModify = false;

                foreach ($chart->getLibs() as $lib) {
                    $oldValue = $data['membre_' . $chart->getIdRecord() . '_' . $lib->getIdLibRecord()];
                    $newValue = '';

                    $nbInput = $lib->getType()->getNbInput();
                    for ($i = 1; $i <= $nbInput; $i++) {
                        $value = $data['value_' . $chart->getIdRecord() . '_' . $lib->getIdLibRecord() . '_' . $i];
                        $newValue .= $value;
                        $values[] = $value;
                    }

                    //@TODO Validateur
                    if (($newValue === '') || (ctype_digit(trim($newValue, '-+')) == false)) {
                        $isNull = true;
                        break;
                    }

                    $newValue = \VideoGamesRecords\CoreBundle\Tools\Score::formToBdd($lib->getType()->getMask(), $values);
                    if ($oldValue === null || ((int)$oldValue !== (int)$newValue) && ($newValue !== '')) {
                        $isModify = true;
                    }

                    $post[$lib->getIdLibRecord() ] =$newValue;

                }

                $em = $this->getDoctrine()->getManager();
                $user = $em->getReference('VideoGamesRecords\CoreBundle\Entity\User', $idMembre);


                if (!$isNull && $isModify) {
                    $userChart = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserChart')->find(
                        array(
                            'idMembre' => $idMembre,
                            'idRecord' => $chart->getIdRecord()
                        )
                    );

                    $isNew = false;
                    if ($userChart == null) {
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
                    $em->flush($userChart);

                    foreach ($chart->getLibs() as $lib) {
                        $userChartLib = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:UserChartLib')->find(
                            array(
                                'idMembre' => $idMembre,
                                'idLibRecord' => $lib->getIdLibRecord()
                            )
                        );
                        if ($userChartLib == null) {
                            $userChartLib = new UserChartLib();
                            $userChartLib->setUser($user);
                            $userChartLib->setLib($lib);

                        }
                        $userChartLib->setValue($post[$lib->getIdLibRecord()]);
                        $em->persist($userChartLib);
                        $em->flush($userChartLib);
                    }

                    $isNew ? $nbInsert++ : $nbUpdate++;


                }

            }

            var_dump('nbInsert=' . $nbInsert);
            var_dump('nbUpdate=' . $nbUpdate);

        }

        exit;
    }

}
