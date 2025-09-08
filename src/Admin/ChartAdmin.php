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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Intl\Locale;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;

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

    private function getLibGame(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libGameFr' : 'libGameEn';
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
            // Informations principales - Colonne 1
            ->with('chart.general.information', [
                'class' => 'col-md-6',
                'label' => 'chart.general.information',
                'box_class' => 'box box-primary'
            ])
            ->add('id', TextType::class, array(
                'label' => 'label.id',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('libChartEn', TextType::class, [
                'label' => 'label.name.en',
                'required' => true,
            ])
            ->add('libChartFr', TextType::class, [
                'label' => 'label.name.fr',
                'required' => false,
            ]);

        // Ajout du groupe selon le contexte
        if ($this->isCurrentRoute('create') || $this->isCurrentRoute('edit')) {
            $btnCatalogue = $this->isCurrentRoute('create');
            $form->add(
                'group',
                ModelListType::class,
                array_merge(
                    $groupOptions,
                    [
                        'data_class' => null,
                        'btn_add' => false,
                        'btn_list' => $btnCatalogue,
                        'btn_edit' => false,
                        'btn_delete' => false,
                        'btn_catalogue' => $btnCatalogue,
                        'label' => 'label.group',
                    ]
                )
            );
        }

        $form->end()

            // Configuration - Colonne 2
            ->with('chart.configuration', [
                'class' => 'col-md-6',
                'label' => 'chart.configuration',
                'box_class' => 'box box-success'
            ])
            ->add('isDlc', CheckboxType::class, [
                'label' => 'label.isDlc',
                'required' => false,
            ])
            ->add('isProofVideoOnly', CheckboxType::class, [
                'label' => 'label.isProofVideoOnly',
                'required' => false,
            ]);

        $form->end()

            // Libs - Section complète
            ->with('label.libs', [
                'class' => 'col-md-12',
                'label' => 'label.libs',
                'box_class' => 'box box-info'
            ])
            ->add('libs', CollectionType::class, array(
                'label' => 'label.libs',
                'by_reference' => false,
                'help' => (($this->isCurrentRoute('create')) ?
                    'label.libs.help' : ''),
                'type_options' => array(
                    'delete' => false,
                    'delete_options' => array(
                        'type' => CheckboxType::class,
                        'type_options' => array(
                            'mapped' => false,
                            'required' => false,
                        )
                    )
                )
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
            ))
            ->end();
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
                'field_options' => ['property' => $this->getLibGroup()],
                'label' => 'label.group',
            ])
            ->add('group.game', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => $this->getLibGame()],
                'label' => 'label.game',
            ])
            ->add('isDlc', null, ['label' => 'label.isDlc'])
            ->add('isProofVideoOnly', null, ['label' => 'label.isProofVideoOnly'])
            ;
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libChartEn', null, ['label' => 'label.chart.en', 'editable' => true])
            ->add('libChartFr', null, ['label' => 'label.chart.fr', 'editable' => true])
            //->add('slug', null, ['label' => 'label.slug'])
            ->add('group', null, array(
                'associated_property' => $this->getLibGroup(),
                'label' => 'label.group',
            ))
            ->add('group.game', null, array(
                'associated_property' => $this->getLibGame(),
                'label' => 'label.game',
            ))
            ->add('isProofVideoOnly', null, ['label' => 'label.isProofVideoOnly', 'editable' => true])
            ->add(
                'libs',
                null,
                [
                    'label' => 'label.libs',
                ]
            )
            ->add('createdAt', 'datetime', ['label' => 'label.createdAt'])
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
            // Informations principales - Colonne 1
            ->with('chart.general.information', [
                'class' => 'col-md-6',
                'label' => 'chart.general.information',
                'box_class' => 'box box-primary'
            ])
            ->add('id', null, ['label' => 'label.id'])
            ->add('libChartEn', null, ['label' => 'label.name.en'])
            ->add('libChartFr', null, ['label' => 'label.name.fr'])
            ->add('group', null, array(
                'associated_property' => $this->getLibGroup(),
                'label' => 'label.group',
            ))
            ->end()

            // Configuration et statuts - Colonne 2
            ->with('chart.configuration', [
                'class' => 'col-md-6',
                'label' => 'chart.configuration',
                'box_class' => 'box box-success'
            ])
            ->add('isDlc', null, ['label' => 'label.isDlc'])
            ->add('isProofVideoOnly', null, ['label' => 'label.isProofVideoOnly'])
            ->end()

            // Métadonnées - Colonne 1 (2ème ligne)
            ->with('chart.metadata', [
                'class' => 'col-md-6',
                'label' => 'chart.metadata',
                'box_class' => 'box box-info'
            ])
            ->add('createdAt', null, ['label' => 'label.createdAt'])
            ->add('updatedAt', null, ['label' => 'label.updatedAt'])
            ->end()

            // Libs - Section complète
            ->with('label.libs', [
                'class' => 'col-md-6',
                'label' => 'label.libs',
                'box_class' => 'box box-warning'
            ])
            ->add('libs', null, ['label' => 'label.libs'])
            ->end();
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
