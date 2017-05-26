<?php
namespace VideoGamesRecords\CoreBundle\Form\Team;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\Team;

class DemandForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('post')
            ->setAction($options['action'])
            ->add('idTeam', HiddenType::class)
            ->add('tag', null, array('label' => 'TAG'))
            ->add('libTeam', null, array('label' => 'Name'))
            ->add('siteWeb', null, array('label' => 'Site Web'))
            ->add(
                'status',
                ChoiceType::class,
                array(
                    'choices'=> array(Team::STATUS_CLOSED => Team::STATUS_CLOSED, Team::STATUS_OPENED => Team::STATUS_OPENED),
                    'data' => Team::STATUS_CLOSED,
                    'label' => 'Status',
                    'expanded' => true,
                )
            )
            ->add('save', SubmitType::class)
        ;
    }
}
