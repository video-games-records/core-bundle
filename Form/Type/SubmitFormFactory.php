<?php

namespace VideoGamesRecords\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use VideoGamesRecords\CoreBundle\Tools\Score;

class SubmitFormFactory
{
    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param \VideoGamesRecords\CoreBundle\Entity\Chart[] $charts
     * @return \Symfony\Component\Form\FormInterface
     */
    public static function createSubmitForm(FormInterface $form, $charts)
    {

        //$data = $form->getData();

        $form
            ->add('id', HiddenType::class)
            ->add('type', HiddenType::class)
            ->add('valid', SubmitType::class);


        foreach ($charts as $chart) {
            $form->add('name_' . $chart->getId(), HiddenType::class, ['label' => $chart->getLibChart()]);

            foreach ($chart->getLibs() as $lib) {
                $id = 'player_' . $chart->getId() . '_' . $lib->getIdLibChart();
                $form->add($id, HiddenType::class); //----- miss ID

                $inputs = Score::getInputs($lib->getType()->getMask());

                $i = 1;
                foreach ($inputs as $k => $input) {
                    $form->add(
                        'value_' . $chart->getId() . '_' . $lib->getIdLibChart() . '_' . $i,
                        TextType::class,
                        [
                            'label' => ($i == 1) ? $lib->getType()->getName() : null,
                            'attr' => [
                                'maxlength' => $input['size'],
                                'size' => 8,
                                'suffixe' => $input['suffixe'],
                            ]
                        ]
                    );//---- missing ID
                    $i++;
                }
            }
        }

        return $form;
    }
}
