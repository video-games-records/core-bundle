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
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\ValueObject\GroupOrderBy;

final class GroupAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_group';

    /**
     * @return string
     */
    private function getLibGame(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libGameFr' : 'libGameEn';
    }

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('export')
            ->add('copy', $this->getRouterIdParameter() . '/copy')
            ->add('add-lib-chart', $this->getRouterIdParameter() . '/add-lib-chart')
            ->add('set-video-proof-only', $this->getRouterIdParameter() . '/set-video-proof-only');
    }

    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        if (in_array($action, ['show', 'edit', 'acl']) && $object) {
            $buttonList['copy'] = [
                'template' => '@VideoGamesRecordsCore/Admin/ActionButton/btn.copy.html.twig',
            ];
            $buttonList['add-lib-chart'] = [
                'template' => '@VideoGamesRecordsCore/Admin/ActionButton/btn.add_lib_chart.html.twig',
            ];
            $buttonList['set-video-proof-only'] = [
                'template' => '@VideoGamesRecordsCore/Admin/ActionButton/btn.set_video_proof_only.html.twig',
            ];
            $buttonList['add-chart'] = [
                'template' => '@VideoGamesRecordsCore/Admin/Object/Group/btn.add_chart.html.twig',
            ];
        }
        return $buttonList;
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $gameOptions = [];
        if (($this->hasRequest()) && ($this->isCurrentRoute('create'))) {
            $idGame = $this->getRequest()->get('idGame', null);
            if ($idGame !== null) {
                $this->getRequest()->getSession()->set('vgrcorebundle_admin_group.idGame', $idGame);
            }

            if ($this->getRequest()->getSession()->has('vgrcorebundle_admin_group.idGame')) {
                $idGame = $this->getRequest()->getSession()->get('vgrcorebundle_admin_group.idGame');
                $entityManager = $this->getModelManager()
                    ->getEntityManager('VideoGamesRecords\CoreBundle\Entity\Game');
                $game = $entityManager->getReference('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);
                $gameOptions = ['data' => $game];
            }
        }

        $form
            // Informations principales - Colonne 1
            ->with('group.general.information', [
                'class' => 'col-md-6',
                'label' => 'group.general.information',
                'box_class' => 'box box-primary'
            ])
            ->add('id', TextType::class, [
                'label' => 'label.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('libGroupEn', TextType::class, [
                'label' => 'label.name.en',
                'required' => true,
            ])
            ->add('libGroupFr', TextType::class, [
                'label' => 'label.name.fr',
                'required' => false,
            ]);

        // Ajout du jeu selon le contexte
        if ($this->isCurrentRoute('create') || $this->isCurrentRoute('edit')) {
            $btnCatalogue = $this->isCurrentRoute('create');
            $form->add(
                'game',
                ModelListType::class,
                array_merge(
                    $gameOptions,
                    [
                        'data_class' => null,
                        'btn_add' => false,
                        'btn_list' => $btnCatalogue,
                        'btn_edit' => false,
                        'btn_delete' => false,
                        'btn_catalogue' => $btnCatalogue,
                        'label' => 'label.game',
                    ]
                )
            );
        }

        $form->end()

            // Configuration - Colonne 2
            ->with('group.configuration', [
                'class' => 'col-md-6',
                'label' => 'group.configuration',
                'box_class' => 'box box-success'
            ])
            ->add('isRank', CheckboxType::class, [
                'label' => 'label.boolRanking',
                'required' => false,
            ])
            ->add(
                'orderBy',
                ChoiceType::class,
                [
                    'label' => 'label.orderBy',
                    'choices' => GroupOrderBy::getStatusChoices(),
                ]
            )
            ->add('isDlc', CheckboxType::class, [
                'label' => 'label.isDlc',
                'required' => false,
            ])
            ->end();

        // Charts - Section complète si présents
        $subject = $this->getSubject();
        if (
            (strpos(
                $this->getRequest()
                        ->getPathInfo(),
                'videogamesrecords/core/group'
            ) || (($this->getRequest()
                            ->getPathInfo() == '/admin/core/append-form-field-element') && ($this->getRequest(
                            )->query->get('_sonata_admin') == 'sonata.admin.vgr.group'))) && (count(
                                $subject->getCharts()
                            ) < 50)
        ) {
            $form
                ->with('label.charts', [
                    'class' => 'col-md-12',
                    'label' => 'label.charts',
                    'box_class' => 'box box-info'
                ])
                ->add(
                    'charts',
                    CollectionType::class,
                    array(
                        'label' => 'label.charts',
                        'by_reference' => false,
                        'help' => 'label.libs.help',
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
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'label.id'])
            ->add('libGroupEn', null, ['label' => 'label.name.en'])
            ->add('libGroupFr', null, ['label' => 'label.name.fr'])
            ->add('isDlc', null, ['label' => 'label.isDlc'])
            ->add(
                'game',
                ModelFilter::class,
                [
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => ['property' => $this->getLibGame()],
                    'label' => 'label.game'
                ]
            )
        ;
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $btns = [];
        if ($this->hasAccess('create')) {
            $btns = [
                'copy' => [
                    'template' => '@VideoGamesRecordsCore/Admin/Object/Group/link.copy.html.twig'
                ],
                'add_chart' => [
                    'template' => '@VideoGamesRecordsCore/Admin/Object/Group/link.add_chart.html.twig'
                ],
            ];
        }

        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libGroupEn', null, ['label' => 'label.group.en', 'editable' => true])
            ->add('libGroupFr', null, ['label' => 'label.group.fr', 'editable' => true])
            ->add('nbChart', null, ['label' => 'label.nbChart'])
            ->add('game', null, [
                'associated_property' => $this->getLibGame(),
                'label' => 'label.game',
            ])
            ->add(
                'orderBy',
                'choice',
                [
                    'label' => 'label.orderBy',
                    'editable' => true,
                    'choices' => GroupOrderBy::getStatusChoices(),
                ]
            )
            ->add('isDlc', 'boolean', ['label' => 'label.isDlc'])
            ->add('_action', 'actions', [
                'actions' =>
                    array_merge(
                        [
                            'show' => [],
                            'edit' => [],
                            'groups' => [
                                'template' => '@VideoGamesRecordsCore/Admin/Object/Group/link.charts.html.twig'
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
            // Informations principales - Colonne 1
            ->with('group.general.information', [
                'class' => 'col-md-6',
                'label' => 'group.general.information',
                'box_class' => 'box box-primary'
            ])
            ->add('id', null, ['label' => 'label.id'])
            ->add('libGroupEn', null, ['label' => 'label.name.en'])
            ->add('libGroupFr', null, ['label' => 'label.name.fr'])
            ->add('game', null, [
                'associated_property' => $this->getLibGame(),
                'label' => 'label.game',
            ])
            ->end()

            // Configuration et statistiques - Colonne 2
            ->with('group.configuration', [
                'class' => 'col-md-6',
                'label' => 'group.configuration',
                'box_class' => 'box box-success'
            ])
            ->add('nbChart', null, ['label' => 'label.nbChart'])
            ->add('isRank', null, ['label' => 'label.boolRanking'])
            ->add('orderBy', null, ['label' => 'label.orderBy'])
            ->add('isDlc', null, ['label' => 'label.isDlc'])
            ->end()

            // Charts - Section complète
            ->with('label.charts', [
                'class' => 'col-md-12',
                'label' => 'label.charts',
                'box_class' => 'box box-info'
            ])
            ->add('charts', null, ['label' => 'label.charts'])
            ->end();
    }
}
