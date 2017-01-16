<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GameAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_game';

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
            ->add('serie', 'sonata_type_model_list', array(
                'btn_add'       => false,
                'btn_list'      => true,
                'btn_delete'    => false,
                'btn_catalogue' => true,
                'label'         => 'Serie',
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
            /*->add('groups', 'sonata_type_collection', array(
                'by_reference' => false,
                'type_options' => array(
                    // Prevents the "Delete" option from being displayed
                    'delete' => true,
                    'delete_options' => array(
                        // You may otherwise choose to put the field but hide it
                        'type'         => 'checkbox',
                        // In that case, you need to fill in the options as well
                        'type_options' => array(
                            'mapped'   => false,
                            'required' => false,
                        )
                    )
                )
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
            ))*/
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
                    'groups' => array(
                        'template' => 'VideoGamesRecordsCoreBundle:Admin:game_groups_link.html.twig'
                    ),
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
            ->add('groups')
         ;
    }

}