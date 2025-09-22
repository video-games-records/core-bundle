<?php

declare(strict_types=1);

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
            ->add('id', TextType::class, [
                'label' => 'chart_type.form.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'chart_type.form.name',
                'required' => true,
            ])
            ->add('mask', TextType::class, [
                'label' => 'chart_type.form.mask',
                'required' => true,
            ])
            ->add(
                'orderBy',
                ChoiceType::class,
                [
                    'label' => 'chart_type.form.orderBy',
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
            ->add('id', null, ['label' => 'chart_type.filter.id'])
            ->add('name', null, ['label' => 'chart_type.filter.name']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'chart_type.list.id'])
            ->add('name', null, ['label' => 'chart_type.list.name'])
            ->add('mask', null, ['label' => 'chart_type.list.mask'])
            ->add('orderBy', null, ['label' => 'chart_type.list.orderBy'])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                ]
            ]);
    }
}
