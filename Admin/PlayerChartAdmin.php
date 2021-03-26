<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Type\CollectionType;

class PlayerChartAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_player_chart';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('export');
    }

    /**
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);

        $rootAlias = current($query->getRootAliases());
        $query
            ->innerJoin($rootAlias[0] . '.chart', 'chart')
            ->addSelect('chart')
            ->innerJoin($rootAlias[0] . '.player', 'player')
            ->addSelect('player')
            ->innerJoin('chart.group', 'grp')
            ->addSelect('grp')
            ->innerJoin('grp.game', 'game')
            ->addSelect('game')
            ->leftJoin('game.translations', 't1', 'WITH', "t1.locale='en'")
            ->addSelect('t1')
            ->leftJoin('grp.translations', 't2', 'WITH', "t2.locale='en'")
            ->addSelect('t2');

        return $query;
    }


    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class, [
                'label' => 'id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('player', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => false,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => false,
                'label' => 'Player',
            ])
            ->add('chart', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Chart',
            ])
            ->add('status', null);

        $form
            ->add('libs', CollectionType::class, array(
                'btn_add' => false,
                'by_reference' => false,
                'type_options' => array(
                    'delete' => false,
                )
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
            ));
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('status')
            ->add('player', ModelAutocompleteFilter::class, array(), null, array(
                'property' => 'pseudo',
            ))
            ->add('chart.group.game', ModelAutocompleteFilter::class, array('label' => 'Game'), null, array(
                'property' => 'translations.name',
            ))
            ->add('chart.id');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'Player',
            ])
            ->add('chart.group.game', null, [
                'associated_property' => 'defaultName',
                'label' => 'Game',
            ])
            ->add('chart.group', null, [
                'associated_property' => 'defaultName',
                'label' => 'Group',
            ])
            ->add('chart', null, [
                'associated_property' => 'defaultName',
                'label' => 'Chart',
            ])
            ->add('status')
            ->add('libs')
            ->add('_action', 'actions', [
                'actions' => [
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
            ->add('id')
            ->add('player')
            ->add('chart');
    }
}
