<?php

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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Intl\Locale;

class GroupAdmin extends AbstractAdmin
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
     * @return string
     */
    private function getLibGroup(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libGroupFr' : 'libGroupEn';
    }

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('export')
            ->add('copy', $this->getRouterIdParameter() . '/copy')
            ->add('copy-with-lib-chart', $this->getRouterIdParameter() . '/copy-with-lib-chart')
            ->add('add-lib-chart', $this->getRouterIdParameter() . '/add-lib-chart');
    }

    /**
     * @param array $actions
     * @return array|\mixed[][]
     */
    /*protected function configureDashboardActions(array $actions): array
    {
        $subject = $this->getSubject();
        $actions['addlibchart'] = [
            'label' => 'add_lib_chart',
            'url' => $this->generateUrl('add-lib-chart', ['id' => $subject->getId()]),
            'icon' => 'import',
            'translation_domain' => 'SonataAdminBundle',
        ];
        return $actions;
    }*/

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

        if ($this->isCurrentRoute('create') || $this->isCurrentRoute('edit')) {
            $btnCalalogue = (bool) $this->isCurrentRoute('create');
            $form->
                add(
                    'game',
                    ModelListType::class,
                    array_merge(
                        $gameOptions,
                    [
                        'data_class' => null,
                        'btn_add' => false,
                        'btn_list' => $btnCalalogue,
                        'btn_edit' => false,
                        'btn_delete' => false,
                        'btn_catalogue' => $btnCalalogue,
                        'label' => 'label.game',
                        ]
                )
            );
        }

        $form->add('boolDLC', CheckboxType::class, [
            'label' => 'label.boolDlc',
            'required' => false,
        ]);

        $subject = $this->getSubject();

        if ((strpos(
                    $this->getRequest()
                        ->getPathInfo(), 'videogamesrecords/core/group'
                ) || (($this->getRequest()
                            ->getPathInfo() == '/admin/core/append-form-field-element') && ($this->getRequest(
                            )->query->get('_sonata_admin') == 'sonata.admin.vgr.group'))) && (count(
                    $subject->getCharts()
                            ) < 50)) {
            $form->end()
                ->with('label.charts')
                ->add(
                    'charts',
                    CollectionType::class,
                    array(
                        'label' => 'label.charts',
                        'by_reference' => false,
                        'help' => 'label.libs.help',
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
                    ), array(
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
            ->add('game', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => $this->getLibGame()],
                'label' => 'label.game'
            ])
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
                    'template' => '@VideoGamesRecordsCore/Admin/group_copy_link.html.twig'
                ],
                'copy2' => [
                    'template' => '@VideoGamesRecordsCore/Admin/group_copy2_link.html.twig'
                ],
                'add_chart' => [
                    'template' => '@VideoGamesRecordsCore/Admin/group_add_chart_link.html.twig'
                ],
            ];
        }

        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libGroupEn', null, ['label' => 'label.group.en', 'editable' => true])
            ->add('libGroupFr', null, ['label' => 'label.group.fr', 'editable' => true])
            //->add('slug', null, ['label' => 'label.slug'])
            ->add('game', null, [
                'associated_property' => $this->getLibGame(),
                'label' => 'label.game',
            ])
            ->add('boolDLC', 'boolean', ['label' => 'label.boolDlc'])
            ->add('_action', 'actions', [
                'actions' =>
                    array_merge(
                        [
                            'show' => [],
                            'edit' => [],
                            'groups' => [
                                'template' => '@VideoGamesRecordsCore/Admin/group_charts_link.html.twig'
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
            ->add('libGroupEn', null, ['label' => 'label.name.en'])
            ->add('libGroupFr', null, ['label' => 'lel.name.fr'])
            ->add('game', null, [
                'associated_property' => $this->getLibGame(),
                'label' => 'label.game',
            ])
            ->add('charts', null, ['label' => 'label.charts']);
    }
}
