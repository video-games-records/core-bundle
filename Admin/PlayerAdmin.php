<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class PlayerAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_player_group';

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('export')
            ->remove('delete');
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
            ->add('pseudo', 'text', [
                'label' => 'pseudo',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('country', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Country',
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('pseudo');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('pseudo')
            ->add('country')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
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
            ->add('pseudo')
            ->add('country')
            ->add('team');
    }
}
