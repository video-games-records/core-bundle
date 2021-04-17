<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\Form\Type\CollectionType;

class GroupAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_group';

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->add('copy', $this->getRouterIdParameter().'/copy')
            ->add('copy-with-lib-chart', $this->getRouterIdParameter().'/copy-with-lib-chart');
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
                $idGame= $this->getRequest()->getSession()->get('vgrcorebundle_admin_group.idGame');
                $entityManager = $this->getModelManager()
                    ->getEntityManager('VideoGamesRecords\CoreBundle\Entity\Game');
                $game = $entityManager->getReference('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);
                $gameOptions = ['data' => $game];
            }
        }

        $form
            ->add('id', TextType::class, [
                'label' => 'idGroup',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('libGroupEn', TextType::class, [
                'label' => 'Name [EN]',
                'required' => true,
            ])
            ->add('libGroupFr', TextType::class, [
                'label' => 'Name [FR]',
                'required' => false,
            ]);

        if ($this->isCurrentRoute('create') || $this->isCurrentRoute('edit')) {
            $btnCalalogue = (bool)$this->isCurrentRoute('create');
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
                        'label' => 'Game',
                    ]
                )
            );
        }

        $form->add('boolDLC', CheckboxType::class, [
            'label' => 'DLC ?',
            'required' => false,
        ]);

        $subject = $this->getSubject();

        if (
        (strpos($this->getRequest()->getPathInfo(), 'videogamesrecords/core/group')
            ||
             (
               ($this->getRequest()->getPathInfo() == '/admin/core/append-form-field-element')
               &&
               ($this->getRequest()->query->get('code') == 'sonata.admin.vgr.group')
            ))
            && (count($subject->getCharts()) < 50)
        ) {
            $form->end()
                ->with('Charts')
                ->add(
                    'charts', CollectionType::class, array(
                        'by_reference' => false,
                        'type_options' => array(
                            // Prevents the "Delete" option from being displayed
                            'delete' => false,
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
            ->add('id')
            ->add('libGroupEn', null, ['label' => 'Name [EN]'])
            ->add('game', ModelAutocompleteFilter::class, [], null, [
                'property' => 'libGameEn',
            ]);
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
                    'template' => 'VideoGamesRecordsCoreBundle:Admin:group_copy_link.html.twig'
                ],
                'copy2' => [
                    'template' => 'VideoGamesRecordsCoreBundle:Admin:group_copy2_link.html.twig'
                ],
                'add_chart' => [
                    'template' => 'VideoGamesRecordsCoreBundle:Admin:group_add_chart_link.html.twig'
                ],
            ];
        }

        $list
            ->addIdentifier('id')
            ->add('libGroupEn', null, ['label' => 'Name'])
            ->add('slug', null, ['label' => 'Slug'])
            ->add('game', null, [
                'associated_property' => 'defaultName',
                'label' => 'Game',
            ])
            ->add('boolDLC', 'boolean')
            ->add('_action', 'actions', [
                'actions' =>
                    array_merge(
                        [
                            'show' => [],
                            'edit' => [],
                            'groups' => [
                                'template' => 'VideoGamesRecordsCoreBundle:Admin:group_charts_link.html.twig'
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
            ->add('id')
            ->add('libGroupEn', null, ['label' => 'Name'])
            ->add('game', null, [
                'associated_property' => 'libGameEn',
                'label' => 'Game',
            ])
            ->add('charts');
    }
}
