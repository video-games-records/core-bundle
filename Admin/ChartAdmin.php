<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
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
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export');
    }

    /**
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);
        $query->innerJoin($query->getRootAliases()[0]  . '.translations', 't', 'WITH', "t.locale='en'")
            ->addSelect('t');
        return $query;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
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

        $formMapper
            ->add('id', TextType::class, array(
                'label' => 'id',
                'attr' => array(
                    'readonly' => true,
                )
            ))
            ->add('group', ModelAutocompleteType::class, [
                'property' => 'translations.name',
            ])
            ->add('group', ModelListType::class, array_merge(
                $groupOptions,
                [
                    'data_class' => null,
                    'btn_add' => false,
                    'btn_list' => true,
                    'btn_edit' => false,
                    'btn_delete' => false,
                    'btn_catalogue' => true,
                    'label' => 'Group',
                ]
            ))
            ->add('translations', TranslationsType::class, [
                'required' => true,
            ]);

        if (($this->hasRequest()) && ($this->isCurrentRoute('edit'))) {
            $formMapper
                ->add(
                    'statusPlayer',
                    ChoiceType::class,
                    array(
                        'label' => 'Status Player',
                        'choices' => Chart::getStatusChoices()
                    )
                );
            $formMapper
                ->add(
                    'statusTeam',
                    ChoiceType::class,
                    array(
                        'label' => 'Status Team',
                        'choices' => Chart::getStatusChoices()
                    )
                );
        }

        $formMapper
            ->add('libs', CollectionType::class, array(
                'by_reference' => false,
                'help' => (($this->isCurrentRoute('create')) ?
                    'If you dont add libs, the libs will be automatically added to the chart by cloning the first chart of the group' : ''),
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
                )
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
            ));
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('translations.name', null, ['label' => 'Name'])
            ->add('group', ModelAutocompleteFilter::class, array(), null, array(
                'property' => 'translations.name',
            ))
            ->add('statusPlayer', 'doctrine_orm_choice', array(), ChoiceType::class, array('choices' => Chart::getStatusChoices()))
            ->add('statusTeam', 'doctrine_orm_choice', array(), ChoiceType::class, array('choices' => Chart::getStatusChoices()));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'translations',
                null,
                [
                    'associated_property' => 'name',
                    'label' => 'Name'
                ]
            )
            ->add('slug', null, ['label' => 'Slug'])
            ->add('group', null, array(
                'associated_property' => 'defaultName',
                'label' => 'Group',
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                )
            ));
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('defaultName', null, ['label' => 'Name'])
            ->add('group', null, array(
                'associated_property' => 'defaultName',
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
