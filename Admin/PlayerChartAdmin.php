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
use Sonata\Form\Type\CollectionType;

class PlayerChartAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'vgrcorebundle_admin_player_chart';

    /**
     * @return string
     */
    private function getLibGame(): string
    {
        return ($this->getRequest()->getLocale() == 'fr') ? 'libGameFr' : 'libGameEn';
    }

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
        $subject = $this->getSubject();
        $this->getRequest()->getSession()->set('vgrcorebundle_admin_playerChart.subject', $subject);

        $form
            ->add('id', TextType::class, [
                'label' => 'id',
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
                'label' => 'Player',
            ])
            ->add('chart', ModelListType::class, [
                'btn_add' => false,
                'btn_list' => true,
                'btn_edit' => false,
                'btn_delete' => false,
                'btn_catalogue' => true,
                'label' => 'Chart',
            ])
            ->add('status');

        $form
            ->add('libs', CollectionType::class, array(
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
            ->add('id')
            ->add('status')
            ->add('player', ModelAutocompleteFilter::class, array(), null, array(
                'property' => 'pseudo',
            ))
            ->add('chart.group.game', ModelAutocompleteFilter::class, array('label' => 'Game'), null, array(
                'property' => 'libGameEn',
            ))
            ->add('chart.id');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('player', null, [
                'associated_property' => 'pseudo',
                'label' => 'Player',
            ])
            ->add('chart.group.game', null, [
                'associated_property' =>  $this->getLibGame(),
                'label' => 'Game',
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
                'label' => 'Group',
                'sortable' => true,
                'sort_field_mapping' => array(
                    'fieldName' => $this->getLibGroup()
                ),
                'sort_parent_association_mappings' => array(
                    array('fieldName' => 'chart'),
                    array('fieldName' => 'group')
                )
            ])

            /*->add('chart.group.game', null, [
                'associated_property' => 'libGameEn',
                'label' => 'Game',
            ])
            ->add('chart.group', null, [
                'associated_property' => 'libGroupEn',
                'label' => 'Group',
            ])*/
            ->add('chart', null, [
                'associated_property' => 'libChartEn',
                'label' => 'Chart',
            ])
            ->add('status')
            ->add('libs')
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
            ->add('id')
            ->add('player')
            ->add('chart')
            ->add('status')
            ->add('dateInvestigation')
            ->add('proof')
            ->add('libs');
    }
}
