<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
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
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libGroupFr' : 'libGroupEn';
    }

    /**
     * @return string
     */
    private function getLibChart(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libChartFr' : 'libChartEn';
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
                'label' => 'label.id',
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
                        'label' => 'label.group',
                    ]
                )
            );
        }

        $form
            ->add('libChartEn', TextType::class, [
                'label' => 'label.name.en',
                'required' => true,
            ])
            ->add('libChartFr', TextType::class, [
                'label' => 'label.name.fr',
                'required' => false,
            ]);

        if ($this->isCurrentRoute('create') || $this->isCurrentRoute('edit')) {
            $form
                ->add(
                    'statusPlayer', ChoiceType::class, array(
                        'label' => 'label.chart.statusPlayer',
                        'choices' => Chart::getStatusChoices()
                    )
                )
                ->add(
                    'statusTeam', ChoiceType::class, array(
                        'label' => 'label.chart.statusTeam',
                        'choices' => Chart::getStatusChoices()
                    )
                );
        }


        $form
            ->add('libs', CollectionType::class, array(
                'label' => 'label.libs',
                'by_reference' => false,
                'help' => (($this->isCurrentRoute('create')) ?
                    'label.libs.help' : ''),
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
            ->add('id', null, ['label' => 'label.id'])
            ->add($this->getLibChart(), null, ['label' => 'label.name'])
            ->add('group', ModelFilter::class, [
                 'field_type' => ModelAutocompleteType::class,
                 'field_options' => ['property'=>$this->getLibGroup()],
                 'label' => 'label.group',
            ])
            ->add(
                'statusPlayer',
                ChoiceFilter::class,[
                    'label' => 'label.chart.statusPlayer',
                    'field_type' => ChoiceType::class,
                    'field_options' => [
                        'choices' => Chart::getStatusChoices(),
                        'multiple' => true,
                        'expanded' => false,
                        'choice_translation_domain' => true,
                    ]

                ]
            )
            ->add(
                'statusTeam',
                ChoiceFilter::class,[
                    'label' => 'label.chart.statusTeam',
                    'field_type' => ChoiceType::class,
                    'field_options' => [
                        'choices' => Chart::getStatusChoices(),
                        'multiple' => true,
                        'expanded' => false,
                        'choice_translation_domain' => true,
                    ]

                ]
            );
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libChartEn', null, ['label' => 'label.chart.en','editable' => true])
            ->add('libChartFr', null, ['label' => 'label.chart.fr','editable' => true])
            //->add('slug', null, ['label' => 'label.slug'])
            ->add('group', null, array(
                'associated_property' => $this->getLibGroup(),
                'label' => 'label.group',
            ))
            ->add(
                'libs',
                null,
                [
                    'label' => 'label.libs',
                ]
            )
            ->add('updated_at', 'datetime', ['label' => 'label.updatedAt'])
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
            ->add('id', null, ['label' => 'label.id'])
            ->add('libChartEn', null, ['label' => 'label.name.en'])
            ->add('libChartFr', null, ['label' => 'label.name.fr'])
            ->add('statusPlayer', null, ['label' => 'label.chart.statusPlayer'])
            ->add('statusTeam', null, ['label' => 'label.chart.statusTeam'])
            ->add('group', null, array(
                'associated_property' => $this->getLibGroup(),
                'label' => 'label.group',
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
