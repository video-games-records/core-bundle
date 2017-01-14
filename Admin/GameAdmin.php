<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GameAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('idGame', 'text', array(
                'label' => 'idGame',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('libGameEn', 'text', array(
                'label' => 'Name (EN)',
                'required' => true,
            ))
            ->add('libGameFr', 'text', array(
                'label' => 'Name (FR)',
                'required' => false,
            ))
            ->add('picture', 'text', array(
                'label' => 'Picture',
                'required' => false,
            ))
            ->add(
                'status',
                ChoiceType::class,
                array(
                    'label' => 'Status',
                    'choices' => array(
                        'ACTIF' => 'ACTIF',
                        'INACTIF' => 'INACTIF',
                    )
                )
            )
            ->add(
                'etat',
                ChoiceType::class,
                array(
                    'label' => 'Status',
                    'choices' => array(
                        'CREATION' => 'CREATION',
                        'RECORD' => 'RECORD',
                        'IMAGE' => 'IMAGE',
                        'FINI' => 'FINI'
                    )
                )
            )
            ->add('platforms', null, array('required' => false, 'expanded' => false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libGameFr')
            ->add('libGameEn')
            ->add('status')
            ->add('etat')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idGame')
            ->add('libGameEn', null, array('editable' => false))
            ->add('libGameFr')
            ->add(
                'picture',
                'text',
                array(
                    'label' => 'Picture',
                    'editable' => true
                )
            )
            ->add(
                'status',
                'choice',
                array(
                    'label' => 'Status',
                    'editable' => true,
                    'choices' => array(
                        'ACTIF' => 'ACTIF',
                        'INACTIF' => 'INACTIF',
                    )
                )
            )
            ->add(
                'etat',
                'choice',
                array(
                    'label' => 'Etat',
                    'editable' => false,
                    'choices' => array(
                        'CREATION' => 'CREATION',
                        'RECORD' => 'RECORD',
                        'IMAGE' => 'IMAGE',
                        'FINI' => 'FINI'
                    )
                )
            )

            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }

    // Fields to be shown on show action
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('idGame')
            ->add('libGameFr')
            ->add('libGameEn')
            ->add('status')
            ->add('etat')
        ;
    }

}