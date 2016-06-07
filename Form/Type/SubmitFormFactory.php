<?php

namespace VideoGamesRecords\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SubmitFormFactory
{
    public static function createSubmitForm($form, $charts)
    {

        //$data = $form->getData();

        $form
            ->add('id', HiddenType::class)
            ->add('type', HiddenType::class)
            ->add('valid', SubmitType::class);


        foreach ($charts as $chart) {
            $form->add('name_' . $chart->getIdChart(), HiddenType::class, array('label' => $chart->getLibChart()));

            foreach ($chart->getLibs() as $lib) {
                $id = 'user_' . $lib->getIdChart() . '_' . $lib->getIdLibChart();
                $form->add($id, HiddenType::class); //----- miss ID

                $inputs = \VideoGamesRecords\CoreBundle\Tools\Score::getInputs($lib->getType()->getMask());

                $i = 1;
                foreach ($inputs as $k => $input) {
                    $form->add(
                        'value_' . $lib->getIdChart() . '_' . $lib->getIdLibChart() . '_' . $i,
                        TextType::class,
                        array(
                            'label' => ($i== 1) ? $lib->getType()->getLib() : null,
                            'attr' => array(
                                'maxlength' => $input['size'],
                                'size' => 8,
                                'suffixe' => $input['suffixe'],
                            )
                        )
                    );//---- missing ID
                    $i++;
                }
            }
        }

        return $form;
    }
}