<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlatformAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection) : void
    {
        $collection
            ->remove('export');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form) : void
    {
        $form
            ->add('id', TextType::class, [
                'label' => 'id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('libPlatform', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('picture', TextType::class, [
                'label' => 'Picture',
                'required' => true,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => [
                        'ACTIF' => 'ACTIF',
                        'INACTIF' => 'INACTIF',
                    ]
                ]
            );
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter) : void
    {
        $filter
            ->add('libPlatform');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list) : void
    {
        $list
            ->addIdentifier('id')
            ->add('libPlatform', null, ['editable' => false])
            ->add('slug')
            ->add(
                'picture',
                'text',
                [
                    'label' => 'Picture',
                    'editable' => true
                ]
            )
            ->add(
                'badge.picture',
                null,
                [
                    'label' => 'Badge',
                    'editable' => false
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'Status',
                    'editable' => false,
                    'choices' => [
                        'ACTIF' => 'ACTIF',
                        'INACTIF' => 'INACTIF',
                    ]
                ]
            )
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show) : void
    {
        $show
            ->add('idPlatform')
            ->add('libPlatform')
            ->add('picture')
            ->add('status')
            ->add('badge');
    }
}
