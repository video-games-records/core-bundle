<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChartTypeAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete')
            ->remove('export');
    }

    /**
     * @inheritdoc
     */
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
            );
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('idType')
            ->add('name');
    }

    /**
     * @inheritdoc
     */
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
            ));
    }
}
