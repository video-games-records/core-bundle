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
use VideoGamesRecords\CoreBundle\ValueObject\TeamRequestStatus;

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
                    'btn_edit' => false,
                    'btn_list' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'team_request.form.team',
                ]
            )
            ->add(
                'player',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_edit' => false,
                    'btn_list' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'team_request.form.player',
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'team_request.form.status',
                    'choices' => TeamRequestStatus::getStatusChoices(),
                ]
            );
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'team_request.filter.id'])
            ->add('status', null, ['label' => 'team_request.filter.status']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'team_request.list.id'])
            ->add('team', null, [
                'associated_property' => 'libTeam',
                'label' => 'team_request.list.team',
            ])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'team_request.list.player',
            ])
            ->add(
                'status',
                'choice',
                [
                    'label' => 'team_request.list.status',
                    'editable' => false,
                    'choices' => TeamRequestStatus::getStatusChoices(),
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
            ->add('id', null, ['label' => 'team_request.show.id'])
            ->add('team', null, [
                'associated_property' => 'libTeam',
                'label' => 'team_request.show.team',
            ])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'team_request.show.player',
            ])
            ->add('status', null, ['label' => 'team_request.show.status']);
    }
}
