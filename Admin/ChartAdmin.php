<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;

class ChartAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_chart';

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export');
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $groupOptions = [];
        if (($this->hasRequest()) && ($this->isCurrentRoute('create'))) {
            $idGroup = $this->getRequest()->get('idGroup', null);

            if ($idGroup !== null) {
                $this->getRequest()->getSession()->set('vgrcorebundle_admin_chart.idGroup', $idGroup);
            }

            if ($this->getRequest()->getSession()->has('vgrcorebundle_admin_chart.idGroup')) {
                $idGroup = $this->getRequest()->getSession()->get('vgrcorebundle_admin_chart.idGroup');
                $entityManager = $this->getModelManager()
                    ->getEntityManager('VideoGamesRecords\CoreBundle\Entity\Group');
                $group = $entityManager->getReference('VideoGamesRecords\CoreBundle\Entity\Group', $idGroup);
                $groupOptions = ['data' => $group];
            }
        }

        $formMapper
            ->add('idChart', 'text', [
                'label' => 'idChart',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('group', 'sonata_type_model_list', array_merge(
                $groupOptions,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => true,
                    'btn_delete' => false,
                    'btn_catalogue' => true,
                    'label' => 'Group',
                    'required' => true,
                ]
            ), [
                'placeholder' => 'No group selected'
            ])
            ->add('libChartEn', 'text', [
                'label' => 'Name (EN)',
                'required' => true,
            ])
            ->add('libChartFr', 'text', [
                'label' => 'Name (FR)',
                'required' => false,
            ]);

        if (($this->hasRequest()) && ($this->isCurrentRoute('edit'))) {
            $formMapper
                ->add(
                    'statusUser',
                    'choice',
                    [
                        'label' => 'Status User',
                        'choices' => Chart::getStatusChoices()
                    ]
                );
            $formMapper
                ->add(
                    'statusTeam',
                    'choice',
                    [
                        'label' => 'Status Team',
                        'choices' => Chart::getStatusChoices()
                    ]
                );
        }

        $formMapper
            ->add('libs', 'sonata_type_collection', [
                'by_reference' => false,
                'help' => (($this->isCurrentRoute('create')) ? 'If you dont add libs, the libs will be automatically added to the chart by cloning the first chart of the group' : ''),
                'type_options' => [
                    // Prevents the "Delete" option from being displayed
                    'delete' => true,
                    'delete_options' => [
                        // You may otherwise choose to put the field but hide it
                        'type' => 'checkbox',
                        // In that case, you need to fill in the options as well
                        'type_options' => [
                            'mapped' => false,
                            'required' => false,
                        ]
                    ]
                ]
            ], [
                'edit' => 'inline',
                'inline' => 'table',
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libChartEn')
            ->add('libChartFr')
            ->add('group', 'doctrine_orm_model_autocomplete', [], null, [
                'property' => 'libGroupEn',
            ])
            ->add('statusUser', 'doctrine_orm_choice', [], 'choice', ['choices' => Chart::getStatusChoices()])
            ->add('statusTeam', 'doctrine_orm_choice', [], 'choice', ['choices' => Chart::getStatusChoices()]);
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('idChart')
            ->add('libChartEn', null, ['editable' => false])
            ->add('libChartFr')
            ->add('group', null, [
                'associated_property' => 'libGroupEn',
                'label' => 'Group',
            ])
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
            ->add('idChart')
            ->add('libChartFr')
            ->add('libChartEn')
            ->add('group', null, [
                'associated_property' => 'libGroupEn',
                'label' => 'Group',
            ]);
    }

    /**
     * @param \VideoGamesRecords\CoreBundle\Entity\Chart $object
     */
    public function prePersist($object)
    {
        $libs = $object->getLibs();
        if (count($libs) == 0) {
            $group = $object->getGroup();
            if ($group !== null) {
                $charts = $group->getCharts();
                if (count($charts) > 0) {
                    $chart = $charts[0];
                    foreach ($chart->getLibs() as $oldLib) {
                        $newLib = new ChartLib();
                        $newLib->setName($oldLib->getName());
                        $newLib->setType($oldLib->getType());
                        $object->addLib($newLib);
                    }
                }
            }
        }
    }
}
