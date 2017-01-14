<?php
namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChartTypeAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('idType', 'text', array(
                'label' => 'idType',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('name', 'text', array(
                'label' => 'Name',
                'required' => true,
            ))
            ->add('mask', 'text', array(
                'label' => 'Mask',
                'required' => true,
            ))
            ->add(
                'orderBy',
                ChoiceType::class,
                array(
                    'label' => 'orderBy',
                    'choices' => array(
                        'ASC' => 'ASC',
                        'DESC' => 'DESC',
                    )
                )
            )
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('idType')
            ->add('name')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idType')
            ->add('name')
            ->add('mask')
            ->add('orderBy')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                )
            ))
        ;
    }

    // Fields to be shown on show action
    protected function configureShowFields(ShowMapper $showMapper)
    {

    }

}