<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\TeamRequest;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Form\Type\ModelListType;

class TeamRequestAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_team_request';

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
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
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
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('status');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
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
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
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
