<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\TeamRequest;
use Sonata\AdminBundle\Form\Type\ModelListType;

class TeamRequestAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_team_request';

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
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add(
                'team',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'Leader',
                ]
            )
            ->add(
                'player',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'Leader',
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => TeamRequest::getStatusChoices(),
                ]
            );
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('status');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('team', null, [
                'associated_property' => 'libTeam',
                'label' => 'Team',
            ])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'Player',
            ])
            ->add(
                'status',
                'choice',
                [
                    'label' => 'Status',
                    'editable' => false,
                    'choices' => TeamRequest::getStatusChoices(),
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
            ->add('id')
            ->add('team', null, [
                'associated_property' => 'libTeam',
                'label' => 'Team',
            ])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'Player',
            ])
            ->add('status');
    }
}
