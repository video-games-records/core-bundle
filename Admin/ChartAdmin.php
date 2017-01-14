<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

class ChartAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('idChart', 'text', array(
                'label' => 'idChart',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('group', 'sonata_type_model_list', array(
                'btn_add'       => false,
                'btn_list'      => true,
                'btn_delete'    => false,
                'btn_catalogue' => true,
                'label'         => 'Group',
            ))
            ->add('libChartEn', 'text', array(
                'label' => 'Name (EN)',
                'required' => true,
            ))
            ->add('libChartFr', 'text', array(
                'label' => 'Name (FR)',
                'required' => false,
            ))
            ->add('libs', 'sonata_type_collection', array(
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
            ))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libChartEn')
            ->add('libChartFr')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idChart')
            ->add('libChartEn', null, array('editable' => false))
            ->add('libChartFr')
            ->add('group', null, array(
                'associated_property'       => 'libGroupEn',
                'label'         => 'Group',
            ))
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
            ->add('idChart')
            ->add('libChartFr')
            ->add('libChartEn')
            ->add('group', null, array(
                'associated_property'       => 'libGroupEn',
                'label'         => 'Group',
            ))
        ;
    }

}