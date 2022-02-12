<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlayerBadgeAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_player_badge';

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection): void
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
                'label' => 'label.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('player', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => false,
                'label' => 'label.player',
            ])
             ->add('badge', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.badge',
            ])
        ;
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('player', ModelAutocompleteFilter::class, ['label' => 'label.player'], null, array(
                'property' => 'pseudo',
            ))
            ->add('badge.game.libGameFr', null, ['label' => 'label.game.fr'])
            ->add('badge.game.libGameEn', null, ['label' => 'label.game.en'])
            ->add('badge.value', null, ['label' => 'label.value'])
            ->add('badge.type', null, ['label' => 'label.type']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'label.player',
            ])
            ->add('badge.type', null, [
                'label' => 'label.type',
            ])
            ->add('badge.game', null, [
                'label' => 'label.game',
            ])
            ->add('badge.country', null, [
                'label' => 'label.country',
            ])
            ->add('badge.value', null, [
                'label' => 'label.value',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'show' => [],
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
            ->add('player', null, ['label' => 'label.player'])
            ->add('badge', null, ['label' => 'label.badge'])
            ->add('badge.type', null, ['label' => 'label.type'])
            ->add('badge.game', null, ['label' => 'label.game'])
            ->add('badge.country', null, ['label' => 'label.country'])
            ->add('badge.value', null, ['label' => 'label.value']);
    }
}
