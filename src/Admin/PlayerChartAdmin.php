<?php

namespace VideoGamesRecords\CoreBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Intl\Locale;

class PlayerChartAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_player_chart';

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
     * @return string
     */
    private function getLibChart(): string
    {
        $locale = Locale::getDefault();
        return ($locale == 'fr') ? 'libChartFr' : 'libChartEn';
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('export');
    }

    /**
     * @param ProxyQueryInterface $query
     * @return ProxyQueryInterface
     */
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);

        $rootAlias = current($query->getRootAliases());
        $query
            ->innerJoin($rootAlias[0] . '.chart', 'chart')
            ->addSelect('chart')
            ->innerJoin($rootAlias[0] . '.player', 'player')
            ->addSelect('player')
            ->innerJoin('chart.group', 'grp')
            ->addSelect('grp')
            ->innerJoin('grp.game', 'game')
            ->addSelect('game');

         return $query;
    }


    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', TextType::class, [
                'label' => 'label.id',
                'attr' => [
                    'readonly' => true,
                ]
            ])
            ->add('player', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => false,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => false,
                'label' => 'label.player',
            ])
            ->add('chart', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'label.chart',
            ])
            ->add('status', null, ['label' => 'label.status'])
            ->add('libs', CollectionType::class, array(
                'label' => 'label.libs',
                'btn_add' => true,
                'by_reference' => false,
                'type_options' => array(
                    'delete' => false,
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
            ->add('status', null, ['label' => 'label.status'])
            ->add('player', ModelFilter::class, [
                 'label' => 'label.player',
                 'field_type' => ModelAutocompleteType::class,
                 'field_options' => ['property'=>'pseudo'],
            ])
            ->add('chart.group.game', ModelFilter::class, [
                 'label' => 'label.game',
                 'field_type' => ModelAutocompleteType::class,
                 'field_options' => ['property'=>'libGameEn'],
            ])
            ->add('chart.group', ModelFilter::class, [
                 'label' => 'label.group',
                 'field_type' => ModelAutocompleteType::class,
                 'field_options' => ['property'=>'libGroupEn'],
            ])
            ->add('chart.id', null, ['label' => 'label.chart.id'])
            ->add('chart.libChartEn', null, ['label' => 'label.name.en'])
            ->add('chart.libChartFr', null, ['label' => 'label.name.fr']);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'label.player',
            ])
            ->add('chart.group.game', null, [
                'associated_property' =>  $this->getLibGame(),
                'label' => 'label.game',
                'sortable' => true,
                'sort_field_mapping' => array(
                    'fieldName' => $this->getLibGame()
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'chart'),
                    array('fieldName' => 'group'),
                    array('fieldName' => 'game'),
                )
            ])
            ->add('chart.group', null, [
                'associated_property' =>  $this->getLibGroup(),
                'label' => 'label.group',
                'sortable' => true,
                'sort_field_mapping' => array(
                    'fieldName' => $this->getLibGroup()
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'chart'),
                    array('fieldName' => 'group')
                )
            ])
            ->add('chart', null, [
                'associated_property' => 'libChartEn',
                'label' => 'label.chart',
            ])
            ->add('status', null, ['label' => 'label.status'])
            ->add('libs', null, ['label' => 'label.libs'])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'show' => [],
                ]
            ]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id', null, ['label' => 'label.id'])
            ->add('player', null, ['label' => 'label.player'])
            ->add('chart', null, ['label' => 'label.chart'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('dateInvestigation', null, ['label' => 'label.dateInvestigation'])
            ->add('proof', null, ['label' => 'label.proof'])
            ->add('libs', null, ['label' => 'label.libs']);
    }

    /**
     * @param $object
     */
    public function preUpdate($object): void
    {
        $chart = $object->getChart();
        $chart->setStatusPlayer('MAJ');
        $chart->setStatusTeam('MAJ');
    }
}
