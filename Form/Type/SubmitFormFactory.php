<?php

namespace VideoGamesRecords\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;
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


        // platform
        $chart = $charts[0];

        $form->add('platform', EntityType::class, array(
            'required'   => false,
            'class' => Platform::class,
            'choice_label' => 'libPlatform',
            //'choices' => $chart->getGroup()->getGame()->getPlatforms()
        ));

        foreach ($charts as $chart) {
            $form->add('name_' . $chart->getId(), HiddenType::class, ['label' => $chart->getLibChart()]);

            foreach ($chart->getLibs() as $lib) {
                $id = 'player_' . $chart->getId() . '_' . $lib->getIdLibChart();
                $form->add($id, HiddenType::class); //----- miss ID

                $inputs = Score::parseChartMask($lib->getType()->getMask());

                $i = 1;
                foreach ($inputs as $k => $input) {
                    $form->add(
                        'value_' . $chart->getId() . '_' . $lib->getIdLibChart() . '_' . $i,
                        TextType::class,
                        [
                            'required'   => false,
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
