<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

class PictureAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_picture';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->remove('export');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', null, ['label' => 'label.id'])
            ->add('path', null, ['label' => 'label.path'])
            ->add(
                'player',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'label.player',
                ]
            )
            ->add(
                'game',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'label.game',
                ]
            );
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('id', null, ['label' => 'label.id']);
    }


    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('path', null, ['label' => 'label.path'])
            ->add('player', null, ['label' => 'label.player'])
            ->add('game', null, ['label' => 'label.game'])
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
            ->add('id', null, ['label' => 'label.id'])
            ->add('path', null, ['label' => 'label.path'])
            ->add('player', null, ['label' => 'label.player'])
            ->add('game', null, ['label' => 'label.game']);
    }
}
