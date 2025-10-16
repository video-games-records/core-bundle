<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use VideoGamesRecords\CoreBundle\Form\Type\RichTextEditorType;
use VideoGamesRecords\CoreBundle\ValueObject\SerieStatus;

class SerieAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->add('maj', $this->getRouterIdParameter() . '/maj');
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
                'label' => 'serie.form.name',
                'required' => true,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'serie.form.status',
                    'choices' => SerieStatus::getStatusChoices(),
                ]
            )
            ->add('picture', TextType::class, [
                'label' => 'serie.form.picture',
                'required' => false,
            ])
            ->add('badge', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'serie.form.badge',
            ])
            ->add('translations', TranslationsType::class, [
                'label' => 'serie.form.translations',
                'fields' => [
                    'description' => [
                        'field_type' => RichTextEditorType::class,
                        'label' => 'serie.form.description',
                        'required' => false,
                   ]
                ]
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'serie.filter.id'])
            ->add('libSerie', null, ['label' => 'serie.filter.name'])
            ->add('status', ChoiceFilter::class, [
                'label' => 'serie.filter.status',
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
        $list->addIdentifier('id', null, ['label' => 'serie.list.id'])
            ->add('createdAt', null, ['label' => 'serie.list.createdAt'])
            ->add('libSerie', null, ['label' => 'serie.list.name'])
            ->add('status', null, ['label' => 'serie.list.status'])
            ->add('picture', null, ['label' => 'serie.list.picture', 'editable' => true])
            ->add('badge.picture', null, ['label' => 'serie.list.badge', 'editable' => true])
            ->add('nbGame', null, ['label' => 'serie.list.nbGame'])
            ->add('nbChart', null, ['label' => 'serie.list.nbChart'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'maj' => [
                        'template' => '@VideoGamesRecordsCore/Admin/object_maj_link.html.twig'
                    ],
                    'history' => [
                        'template' => '@VideoGamesRecordsCore/Admin/Object/Serie/link.history.html.twig'
                    ],
                    'games' => [
                        'template' => '@VideoGamesRecordsCore/Admin/Object/Serie/link.games.html.twig'
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
            ->add('libSerie', null, ['label' => 'serie.show.name'])
            ->add('createdAt', null, ['label' => 'serie.show.createdAt'])
            ->add('updatedAt', null, ['label' => 'serie.show.updatedAt'])
            ->add('games', null, ['label' => 'serie.show.games'])
            ->add('nbGame', null, ['label' => 'serie.show.nbGame'])
            ->add('nbChart', null, ['label' => 'serie.show.nbChart'])
            ->add('status', null, ['label' => 'serie.show.status'])
            ->add('picture', null, ['label' => 'serie.show.picture'])
            ->add('badge', null, ['label' => 'serie.show.badge'])
            ->add('getDescription', null, ['label' => 'serie.show.description']);
    }
}
