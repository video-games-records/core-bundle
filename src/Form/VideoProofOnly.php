<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class VideoProofOnly extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'isVideoProofOnly',
            ChoiceType::class,
            [
                'choices'  => [
                    'Yes' => true,
                    'No' => false,
                ]
            ]
        )
            ->add('submit', SubmitType::class);
    }
}
