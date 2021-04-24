<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\Form\Type\CollectionType;

class ChartAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_chart';

    /**
     * @return string
     */
    private function getLibGroup(): string
    {
        return ($this->getRequest()->getLocale() == 'fr') ? 'libGroupFr' : 'libGroupEn';
    }

    /**
     * @return string
     */
    private function getLibChart(): string
    {
        return ($this->getRequest()->getLocale() == 'fr') ? 'libChartFr' : 'libChartEn';
    }

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $groupOptions = array();
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
                $groupOptions = array('data' => $group);
            }
        }

        $form
            ->add('id', TextType::class, array(
                'label' => 'id',
                'attr' => array(
                    'readonly' => true,
                )
            ));

        if ($this->isCurrentRoute('create') || $this->isCurrentRoute('edit')) {
            $btnCalalogue = (bool)$this->isCurrentRoute('create');
            $form->
                add(
                    'group', ModelListType::class, array_merge(
                    $groupOptions, [
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

        $form
            ->add('libChartEn', TextType::class, [
                'label' => 'Name [EN]',
                'required' => true,
            ])
            ->add('libChartFr', TextType::class, [
                'label' => 'Name [FR]',
                'required' => false,
            ]);

        if ($this->isCurrentRoute('create') || $this->isCurrentRoute('edit')) {
            $form
                ->add(
                    'statusPlayer', ChoiceType::class, array(
                        'label' => 'Status Player',
                        'choices' => Chart::getStatusChoices()
                    )
                )
                ->add(
                    'statusTeam', ChoiceType::class, array(
                        'label' => 'Status Team',
                        'choices' => Chart::getStatusChoices()
                    )
                );
        }


        $form
            ->add('libs', CollectionType::class, array(
                'by_reference' => false,
                'help' => (($this->isCurrentRoute('create')) ?
                    'If you dont add libs, the libs will be automatically added to the chart by cloning the first chart of the group' : ''),
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
                )
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
            ));
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add($this->getLibChart(), null, ['label' => 'Name'])
            ->add('group', ModelAutocompleteFilter::class, [], null, [
                'property' => $this->getLibGroup(),
            ])
            ->add('statusPlayer', 'doctrine_orm_choice', array(), ChoiceType::class, array('choices' => Chart::getStatusChoices()))
            ->add('statusTeam', 'doctrine_orm_choice', array(), ChoiceType::class, array('choices' => Chart::getStatusChoices()));
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add($this->getLibChart(), null, ['label' => 'Name'])
            ->add('slug', null, ['label' => 'Slug'])
            ->add('group', null, array(
                'associated_property' => $this->getLibGroup(),
                'label' => 'Group',
            ))
            ->add(
                'libs',
                null,
                [
                    'label' => 'Libs',
                ]
            )
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                )
            ));
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('libChartEn', null, ['label' => 'Name [EN]'])
            ->add('libChartFr', null, ['label' => 'Name [FR]'])
            ->add('group', null, array(
                'associated_property' => $this->getLibGroup(),
                'label' => 'Group',
            ));
    }

    /**
     * @param $object
     */
    public function prePersist($object): void
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
