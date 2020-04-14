<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\Entity\Team;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Form\Type\ModelListType;

class TeamAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_team';

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
            ->add('libTeam', TextType::class, [
                'label' => 'libTeam',
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
                    'label' => 'Leader',
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'Status',
                    'choices' => Team::getStatusChoices(),
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
            ->add('libTeam');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'libTeam',
                'text',
                [
                    'label' => 'libTeam',
                    'editable' => false
                ]
            )
            ->add('leader', null, [
                'associated_property' => 'pseudo',
                'label' => 'Leader',
            ])
            ->add('nbPlayer')
            ->add(
                'status',
                'choice',
                [
                    'label' => 'Status',
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
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('libTeam')
            ->add('leader', null, [
                'associated_property' => 'pseudo',
                'label' => 'Leader',
            ])
            ->add('status')
            ->add('nbPlayer')
            ->add('players');
    }
}
