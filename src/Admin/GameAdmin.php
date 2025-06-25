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
            ->add('serie', ModelAutocompleteType::class, [
                'property' => 'libSerie',
                'label' => 'label.serie',
                'required' => false,
            ])
            ->add('libGameEn', TextType::class, [
                'label' => 'label.name.en',
                'required' => true,
            ])
            ->add('libGameFr', TextType::class, [
                'label' => 'label.name.fr',
                'required' => false,
            ])
            ->add('rules', null, ['required' => false, 'expanded' => false, 'label' => 'label.rules'])
            ->add('badge', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.badge',
            ])
            ->add('forum', ModelListType::class, [
                'btn_add' => true,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.forum',
            ])
            ->add('picture', TextType::class, [
                'label' => 'label.picture',
                'required' => false,
            ])
            ->add('downloadUrl', TextType::class, [
                'label' => 'label.downloadUrl',
                'required' => false,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'label.status',
                    'choices' => GameStatus::getStatusChoices(),
                ]
            )
            ->add('publishedAt', DateType::class, [
                'label' => 'label.publishedAt',
                'required' => false,
                'years' => range(2004, date('Y'))
            ])
            ->add('isRank', CheckboxType::class, [
                'label' => 'label.boolRanking',
                'required' => false,
            ])
            ->add(
                'platforms',
                null,
                [
                    'label' => 'label.platforms',
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
            ->end()
            ->with('label.groups')
            ->add(
                'groups',
                CollectionType::class,
                array(
                    'label' => 'label.groups',
                    'by_reference' => false,
                    'type_options' => array(
                        // Prevents the "Delete" option from being displayed
                        'delete' => true,
                        'delete_options' => array(
                            // You may otherwise choose to put the field but hide it
                            'type' => CheckboxType::class,
                            // In that case, you need to fill in the options as well
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
            ->add('id', null, ['label' => 'label.id'])
            ->add('serie', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'libSerie'],
                'label' => 'label.serie'
            ])
            ->add('libGameEn', null, ['label' => 'label.name.en'])
            ->add('libGameFr', null, ['label' => 'label.name.fr'])
            ->add('nbChart', null, ['label' => 'label.nbChart'])
            ->add(
                'platforms',
                null,
                [
                    'label' => 'label.platforms',
                    'field_options' => [
                        'multiple' => true,
                    ]
                ]
            )
            ->add('status', ChoiceFilter::class, [
                'label' => 'label.status',
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => GameStatus::getStatusChoices(),
                    'multiple' => false,
                ]
            ])
            ->add('isRank', null, ['label' => 'label.boolRanking']);
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
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libGameEn', null, ['label' => 'label.game.en', 'editable' => true])
            ->add('libGameFr', null, ['label' => 'label.game.fr', 'editable' => true])
            ->add('platforms', null, ['label' => 'label.platforms'])
            ->add(
                'picture',
                null,
                [
                    'label' => 'label.picture',
                    'editable' => true
                ]
            )
            ->add(
                'badge.picture',
                null,
                [
                    'label' => 'label.badge',
                    'editable' => true
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'label.status',
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
            ->add('id', null, ['label' => 'label.id'])
            ->add('libGameEn', null, ['label' => 'label.name.en'])
            ->add('libGameFr', null, ['label' => 'label.name.fr'])
            ->add('platforms', null, ['label' => 'label.platforms'])
            ->add('nbChart', null, ['label' => 'label.nbChart'])
            ->add('nbVideo', null, ['label' => 'label.nbVideo'])
            ->add('downloadUrl', null, ['label' => 'label.downloadUrl'])
            ->add('picture', null, ['label' => 'label.picture'])
            ->add('badge', null, ['label' => 'label.badge'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('forum', null, ['label' => 'label.forum'])
            ->add('groups', null, ['label' => 'label.groups']);
    }
}
