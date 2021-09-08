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
                'label' => 'label.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('libPlatform', TextType::class, [
                'label' => 'label.name',
                'required' => true,
            ])
            ->add('picture', TextType::class, [
                'label' => 'label.picture',
                'required' => true,
            ])
            ->add('badge', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.badge',
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'label.status',
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
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libPlatform', null, ['editable' => false, 'label' => 'label.name'])
            ->add('slug', null, ['label' => 'label.slug'])
            ->add(
                'picture',
                'text',
                [
                    'label' => 'label.picture',
                    'editable' => true
                ]
            )
            ->add(
                'badge.picture',
                null,
                [
                    'label' => 'label.badge',
                    'editable' => false
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'label.status',
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
            ->add('id', null, ['label' => 'label.id'])
            ->add('libPlatform', null, ['label' => 'label.name'])
            ->add('picture', null, ['label' => 'label.picture'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('badge', null, ['label' => 'label.badge']);
    }
}
