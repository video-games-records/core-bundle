<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class PlayerChartAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_player_chart';

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('export');
    }

    /**
     * @param string $context
     * @return mixed
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        if ($context == 'list') {
            $query->innerJoin($query->getRootAliases()[0] . '.chart', 'chart');
            $query->innerJoin('chart.group', 'group');
            $query->innerJoin('group.game', 'game');
        }
        return $query;
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id', 'text', [
                'label' => 'id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('player', 'sonata_type_model_list', [
                'btn_add' => false,
                'btn_list' => false,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => false,
                'label' => 'Player',
            ])
            ->add('chart', 'sonata_type_model_list', [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Chart',
            ]);

        $formMapper
            ->add('libs', 'sonata_type_collection', array(
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
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('player', 'doctrine_orm_model_autocomplete', array(), null, array(
                'property' => 'pseudo',
            ))
            ->add('chart.id');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'Player',
            ])
            ->add('chart', null, [
                'associated_property' => 'defaultName',
                'label' => 'Chart',
            ])
            ->add('libs')
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('player')
            ->add('chart');
    }
}