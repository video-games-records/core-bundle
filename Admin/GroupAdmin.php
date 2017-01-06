<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

class GroupAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('idGroup', 'text', array(
                'label' => 'idGroup',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('game', 'sonata_type_model_list', array(
                'btn_add'       => false,
                'btn_list'      => true,
                'btn_delete'    => false,
                'btn_catalogue' => true,
                'label'         => 'Game',
            ))
            ->add('libGroupEn', 'text', array(
                'label' => 'Name (EN)',
                'required' => true,
            ))
            ->add('libGroupFr', 'text', array(
                'label' => 'Name (FR)',
                'required' => false,
            ))
            ->add('boolDLC', 'checkbox', array(
                'label' => 'DLC ?',
                'required' => false,
            ))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libGroupFr')
            ->add('libGroupEn')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idGroup')
            ->add('libGroupEn', null, array('editable' => false))
            ->add('libGroupFr')
            ->add('game', null, array(
                'associated_property'       => 'libGameEn',
                'label'         => 'Game',
            ))
            ->add('boolDLC', 'boolean')
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
            ->add('idGroup')
            ->add('libGroupFr')
            ->add('libGroupEn')
            ->add('game', null, array(
                'associated_property'       => 'libGameEn',
                'label'         => 'Game',
            ))
        ;
    }

    // Validate fileds
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('libGroupEn')
                ->assertNotBlank()
                ->assertLength(array('max' => 100))
            ->end()
            ->with('libGroupFr')
                ->assertLength(array('max' => 100))
            ->end()
        ;
    }
}