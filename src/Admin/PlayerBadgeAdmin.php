<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlayerBadgeAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_player_badge';

    /**
     * @param RouteCollection $collection
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
                'label' => 'player_badge.form.id',
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
                'label' => 'player_badge.form.player',
            ])
            ->add('badge', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'player_badge.form.badge',
            ])
        ;
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('player', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'pseudo'],
                'label' => 'player_badge.filter.player'
            ])
            ->add('badge.game.libGameFr', null, ['label' => 'player_badge.filter.game.fr'])
            ->add('badge.game.libGameEn', null, ['label' => 'player_badge.filter.game.en'])
            ->add('badge.value', null, ['label' => 'player_badge.filter.value'])
            ->add('badge.type', null, ['label' => 'player_badge.filter.type']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'player_badge.list.id'])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'player_badge.list.player',
            ])
            ->add('badge.type', null, [
                'label' => 'player_badge.list.type',
            ])
            ->add('badge.game', null, [
                'label' => 'player_badge.list.game',
            ])
            ->add('badge.country', null, [
                'label' => 'player_badge.list.country',
            ])
            ->add('badge.value', null, [
                'label' => 'player_badge.list.value',
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
            ->add('id', null, ['label' => 'player_badge.show.id'])
            ->add('player', null, ['label' => 'player_badge.show.player'])
            ->add('badge', null, ['label' => 'player_badge.show.badge'])
            ->add('badge.type', null, ['label' => 'player_badge.show.type'])
            ->add('badge.game', null, ['label' => 'player_badge.show.game'])
            ->add('badge.country', null, ['label' => 'player_badge.show.country'])
            ->add('badge.value', null, ['label' => 'player_badge.show.value']);
    }
}
