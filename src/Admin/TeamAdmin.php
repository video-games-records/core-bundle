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
use VideoGamesRecords\CoreBundle\Entity\Team;

class TeamAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_team';

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
            ->add('libTeam', TextType::class, [
                'label' => 'team.form.libTeam',
                'required' => true,
            ])
            ->add(
                'leader',
                ModelListType::class,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => false,
                    'label' => 'team.form.leader',
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'team.form.status',
                    'choices' => Team::getStatusChoices(),
                ]
            );
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'team.filter.id'])
            ->add('libTeam', null, ['label' => 'team.filter.libTeam']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'team.list.id'])
            ->add(
                'libTeam',
                'text',
                [
                    'label' => 'team.list.libTeam',
                    'editable' => false,
                ]
            )
            ->add('leader', null, [
                'associated_property' => 'pseudo',
                'label' => 'team.list.leader',
            ])
            ->add('nbPlayer', null, ['label' => 'team.list.nbPlayer'])
            ->add(
                'status',
                'choice',
                [
                    'label' => 'team.list.status',
                    'editable' => false,
                    'choices' => Team::getStatusChoices(),
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
            ->add('id', null, ['label' => 'team.show.id'])
            ->add('libTeam', null, ['label' => 'team.show.libTeam'])
            ->add('leader', null, [
                'associated_property' => 'pseudo',
                'label' => 'team.show.leader',
            ])
            ->add('status', null, ['label' => 'team.show.status'])
            ->add('nbPlayer', null, ['label' => 'team.show.nbPlayer'])
            ->add('players', null, ['label' => 'team.show.players']);
    }
}
