<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use VideoGamesRecords\CoreBundle\Contracts\SecurityInterface;
use VideoGamesRecords\CoreBundle\Traits\Accessor\SetSecurity;
use VideoGamesRecords\CoreBundle\ValueObject\GameStatus;

class GameAdmin extends AbstractAdmin implements SecurityInterface
{
    use SetSecurity;

    protected $baseRouteName = 'vgrcorebundle_admin_game';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('copy', $this->getRouterIdParameter() . '/copy')
            ->add('maj', $this->getRouterIdParameter() . '/maj')
            ->add('set-video-proof-only', $this->getRouterIdParameter() . '/set-video-proof-only')
            ->add('import-csv', $this->getRouterIdParameter() . '/import-csv');
    }

    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        if (in_array($action, ['show', 'edit', 'acl']) && $object) {
            if ($this->getSecurity()->isGranted(self::ROLE_SUPER_ADMIN)) {
                $buttonList['copy'] = [
                    'template' => '@VideoGamesRecordsCore/Admin/ActionButton/btn.copy.html.twig',
                ];
            }
            $buttonList['set-video-proof-only'] = [
                'template' => '@VideoGamesRecordsCore/Admin/ActionButton/btn.set_video_proof_only.html.twig',
            ];
            $buttonList['add-group'] = [
                'template' => '@VideoGamesRecordsCore/Admin/Object/Game/btn.add_group.html.twig',
            ];
        }
        return $buttonList;
    }

    /**
     * @param array<mixed> $sortValues
     * @return void
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_page'] = 1;
        $sortValues['_sort_order'] = 'DESC';
        $sortValues['_sort_by'] = 'id';
    }

    /**
     * @return string[]
     */
    protected function configureExportFields(): array
    {
        return ['id', 'libGameEn', 'libGameFr', 'serie', 'status', 'picture', 'platforms'];
    }


    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('section.general.information', [
                'class' => 'col-md-6',
                'label' => 'section.general.information',
                'box_class' => 'box box-primary'
            ])
            ->add('serie', ModelAutocompleteType::class, [
                'property' => 'libSerie',
                'label' => 'game.form.serie',
                'required' => false,
                'btn_add' => false,
            ])
            ->add('libGameEn', TextType::class, [
                'label' => 'game.form.name.en',
                'required' => true,
            ])
            ->add('libGameFr', TextType::class, [
                'label' => 'game.form.name.fr',
                'required' => false,
            ])
            ->end()

            // Configuration et médias - 2ème colonne
            ->with('section.configuration', [
                'class' => 'col-md-6',
                'label' => 'section.configuration',
                'box_class' => 'box box-success'
            ])
            ->add('picture', TextType::class, [
                'label' => 'game.form.picture',
                'required' => false,
            ])
            ->add('downloadUrl', TextType::class, [
                'label' => 'game.form.downloadUrl',
                'required' => false,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'game.form.status',
                    'choices' => GameStatus::getStatusChoices(),
                ]
            )
            ->add('publishedAt', DateType::class, [
                'label' => 'game.form.publishedAt',
                'required' => false,
                'years' => range(2004, date('Y'))
            ])
            ->add('isRank', CheckboxType::class, [
                'label' => 'game.form.boolRanking',
                'required' => false,
            ])
            ->end()

            // Associations - 2 colonnes
            ->with('section.associations', [
                'class' => 'col-md-6',
                'label' => 'section.associations',
                'box_class' => 'box box-info'
            ])
            ->add('badge', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'game.form.badge',
            ])
            ->add('forum', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'game.form.forum',
            ])
            ->add('rules', null, [
                'required' => false,
                'expanded' => false,
                'label' => 'game.form.rules'
            ])
            ->add('igdbGame', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'game.form.igdb_game',
            ])
            ->end()

            // Plateformes - 2ème colonne des associations
            ->with('section.platforms', [
                'class' => 'col-md-6',
                'label' => 'section.platforms',
                'box_class' => 'box box-warning'
            ])
            ->add(
                'platforms',
                null,
                [
                    'label' => 'game.form.platforms',
                    'required' => false,
                    'expanded' => false,
                    'query_builder' =>
                        function ($er) {
                            $qb = $er->createQueryBuilder('p');
                            $qb->orderBy('p.name', 'ASC');
                            return $qb;
                        }
                ]
            )
            ->end();

        $form
            ->with('game.form.groups', [
                'class' => 'col-md-12',
                'label' => 'game.form.groups',
                'box_class' => 'box box-default'
            ])
            ->add(
                'groups',
                CollectionType::class,
                array(
                    'label' => 'game.form.groups',
                    'by_reference' => false,
                    'type_options' => array(
                        'delete' => true,
                        'delete_options' => array(
                            'type' => CheckboxType::class,
                            'type_options' => array(
                                'mapped' => false,
                                'required' => false,
                            )
                        )
                    ),
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                )
            )
            ->end();
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'game.filter.id'])
            ->add('serie', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'libSerie'],
                'label' => 'game.filter.serie'
            ])
            ->add('igdbGame', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'name'],
                'label' => 'game.filter.igdb_game'
            ])
            ->add('libGameEn', null, ['label' => 'game.filter.name.en'])
            ->add('libGameFr', null, ['label' => 'game.filter.name.fr'])
            ->add('nbChart', null, ['label' => 'game.filter.nbChart'])
            ->add(
                'platforms',
                null,
                [
                    'label' => 'game.filter.platforms',
                    'field_options' => [
                        'multiple' => true,
                    ]
                ]
            )
            ->add('status', ChoiceFilter::class, [
                'label' => 'game.filter.status',
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => GameStatus::getStatusChoices(),
                    'multiple' => false,
                ]
            ])
            ->add('isRank', null, ['label' => 'game.filter.boolRanking']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $btns = [
            'maj' => [
                'template' => '@VideoGamesRecordsCore/Admin/object_maj_link.html.twig'
            ],
            'history' => [
                'template' => '@VideoGamesRecordsCore/Admin/Object/Game/link.history.html.twig'
            ],
        ];
        if ($this->hasAccess('create')) {
            if ($this->getSecurity()->isGranted(self::ROLE_SUPER_ADMIN)) {
                $btns['copy'] = [
                    'template' => '@VideoGamesRecordsCore/Admin/Object/Game/link.copy.html.twig'
                ];
            }
            $btns['add_group'] = [
                'template' => '@VideoGamesRecordsCore/Admin/Object/Game/link.add_group.html.twig'
            ];
        }

        $list
            ->addIdentifier('id', null, ['label' => 'game.list.id'])
            ->add('libGameEn', null, ['label' => 'game.list.game.en', 'editable' => true])
            ->add('libGameFr', null, ['label' => 'game.list.game.fr', 'editable' => true])
            ->add('platforms', null, ['label' => 'game.list.platforms'])
            ->add(
                'picture',
                null,
                [
                    'label' => 'game.list.picture',
                    'editable' => true
                ]
            )
            ->add(
                'badge.picture',
                null,
                [
                    'label' => 'game.list.badge',
                    'editable' => true
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'game.list.status',
                    'editable' => true,
                    'choices' => GameStatus::getReverseStatusChoices(),
                ]
            )
            ->add('_action', 'actions', [
                'actions' => array_merge(
                    [
                        'show' => [],
                        'edit' => [],
                        'groups' => [
                            'template' => '@VideoGamesRecordsCore/Admin/Object/Game/link.groups.html.twig'
                        ]
                    ],
                    $btns
                )
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'game.show.id'])
            ->add('libGameEn', null, ['label' => 'game.show.name.en'])
            ->add('libGameFr', null, ['label' => 'game.show.name.fr'])
            ->add('igdbGame', null, ['label' => 'game.show.igdb_game'])
            ->add('platforms', null, ['label' => 'game.show.platforms'])
            ->add('nbChart', null, ['label' => 'game.show.nbChart'])
            ->add('nbVideo', null, ['label' => 'game.show.nbVideo'])
            ->add('downloadUrl', null, ['label' => 'game.show.downloadUrl'])
            ->add('picture', null, ['label' => 'game.show.picture'])
            ->add('badge', null, ['label' => 'game.show.badge'])
            ->add('status', null, ['label' => 'game.show.status'])
            ->add('forum', null, ['label' => 'game.show.forum'])
            ->add('groups', null, ['label' => 'game.show.groups']);
    }
}
