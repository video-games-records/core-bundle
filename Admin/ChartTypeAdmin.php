<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChartTypeAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('delete')
            ->remove('export');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('idType', TextType::class, [
                'label' => 'idType',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('mask', TextType::class, [
                'label' => 'Mask',
                'required' => true,
            ])
            ->add(
                'orderBy',
                ChoiceType::class,
                [
                    'label' => 'orderBy',
                    'choices' => [
                        'ASC' => 'ASC',
                        'DESC' => 'DESC',
                    ]
                ]
            );
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('idType')
            ->add('name');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('idType')
            ->add('name')
            ->add('mask')
            ->add('orderBy')
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                ]
            ]);
    }
}
