<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlatformAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class, [
                'label' => 'platform.form.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'platform.form.name',
                'required' => true,
            ])
            ->add('picture', TextType::class, [
                'label' => 'platform.form.picture',
                'required' => true,
            ])
            ->add('badge', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'platform.form.badge',
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'platform.form.status',
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
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('name', null, ['label' => 'platform.filter.name']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'platform.list.id'])
            ->add('name', null, ['editable' => false, 'label' => 'platform.list.name'])
            ->add('slug', null, ['label' => 'platform.list.slug'])
            ->add(
                'picture',
                'text',
                [
                    'label' => 'platform.list.picture',
                    'editable' => true
                ]
            )
            ->add(
                'badge.picture',
                null,
                [
                    'label' => 'platform.list.badge',
                    'editable' => false
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'platform.list.status',
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
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'platform.show.id'])
            ->add('name', null, ['label' => 'platform.show.name'])
            ->add('picture', null, ['label' => 'platform.show.picture'])
            ->add('status', null, ['label' => 'platform.show.status'])
            ->add('badge', null, ['label' => 'platform.show.badge']);
    }
}
