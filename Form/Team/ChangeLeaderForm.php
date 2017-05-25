<?php
namespace VideoGamesRecords\CoreBundle\Form\Team;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\Team;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeLeaderForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('post')
            ->add(
                'idPlayer',
                ChoiceType::class,
                array(
                    'choices'=> $options['players'],
                    'data' => Team::STATUS_CLOSED,
                    'label' => 'Player',
                    'expanded' => false,
                )
            )
            ->add('save', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('players', null)
            ->setRequired('players')
            ->setAllowedTypes('players', array('array'))
        ;
    }
}