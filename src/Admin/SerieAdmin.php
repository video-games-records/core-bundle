<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use VideoGamesRecords\CoreBundle\ValueObject\SerieStatus;

class SerieAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export');
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_page'] = 1;
        $sortValues['_sort_order'] = 'ASC';
        $sortValues['_sort_by'] = 'libSerie';
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('libSerie', TextType::class, [
                'label' => 'label.name',
                'required' => true,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'label.status',
                    'choices' => SerieStatus::getStatusChoices(),
                ]
            )
            ->add('picture', TextType::class, [
                'label' => 'label.picture',
                'required' => false,
            ])
            ->add('badge', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.badge',
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'label.id'])
            ->add('libSerie', null, ['label' => 'label.name'])
            ->add('status', ChoiceFilter::class, [
                'label' => 'label.status',
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => SerieStatus::getStatusChoices(),
                    'multiple' => false,
                ]
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('createdAt', null, ['label' => 'label.createdAt'])
            ->add('libSerie', null, ['label' => 'label.name'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('picture', null, ['label' => 'label.picture', 'editable' => true])
            ->add('badge.picture', null, ['label' => 'label.badge', 'editable' => true])
            ->add('nbGame', null, ['label' => 'label.nbGame'])
            ->add('nbChart', null, ['label' => 'label.nbChart'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'history' => [
                        'template' => '@VideoGamesRecordsCore/Admin/serie_history_link.html.twig'
                    ],
                    'games' => [
                        'template' => '@VideoGamesRecordsCore/Admin/serie_games_link.html.twig'
                    ],
                ]
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('libSerie', null, ['label' => 'label.name'])
            ->add('createdAt', null, ['label' => 'label.createdAt'])
            ->add('updatedAt', null, ['label' => 'label.updatedAt'])
            ->add('games', null, ['label' => 'label.games'])
            ->add('nbGame', null, ['label' => 'label.nbGame'])
            ->add('nbChart', null, ['label' => 'label.nbChart'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('picture', null, ['label' => 'label.picture'])
            ->add('badge', null, ['label' => 'label.badge']);
    }
}
