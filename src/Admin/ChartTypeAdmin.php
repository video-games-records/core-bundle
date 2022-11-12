<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
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
                'label' => 'label.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name',
                'required' => true,
            ])
            ->add('mask', TextType::class, [
                'label' => 'label.mask',
                'required' => true,
            ])
            ->add(
                'orderBy',
                ChoiceType::class,
                [
                    'label' => 'label.orderBy',
                    'choices' => [
                        'label.orderBy.asc' => 'ASC',
                        'label.orderBy.desc' => 'DESC',
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
            ->add('idType', null, ['label' => 'label.id'])
            ->add('name', null, ['label' => 'label.name']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('idType', null, ['label' => 'label.id'])
            ->add('name', null, ['label' => 'label.name'])
            ->add('mask', null, ['label' => 'label.mask'])
            ->add('orderBy', null, ['label' => 'label.orderBy'])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                ]
            ]);
    }
}
